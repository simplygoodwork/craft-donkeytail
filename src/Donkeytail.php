<?php

/**
 * Donkeytail plugin for Craft CMS 3.x
 *
 * Fieldtype that allows you to quickly and easily content manage points on images. You can use it for locations on a faux map, showcasing multiple products within an image, or even pinning the tail on a donkey.
 *
 * @link      https://simplygoodwork.com
 * @copyright Copyright (c) 2020 Good Work
 */

namespace simplygoodwork\donkeytail;

use Craft;
use yii\base\Event;

use craft\helpers\App;
use craft\base\Plugin;
use craft\services\Gql;
use craft\services\Fields;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\events\RegisterGqlTypesEvent;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterComponentTypesEvent;
use simplygoodwork\donkeytail\gql\DonkeytailType;
use nystudio107\pluginvite\services\VitePluginService;
use simplygoodwork\donkeytail\assetbundles\donkeytail\DonkeytailAsset;
use simplygoodwork\donkeytail\variables\DonkeytailVariable;
use simplygoodwork\donkeytail\fields\Donkeytail as DonkeytailField;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    Good Work
 * @package   Donkeytail
 * @since     1.0.0
 *
 */
class Donkeytail extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Donkeytail::$plugin
     *
     * @var Donkeytail
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     *
     * @var string
     */
    public string $schemaVersion = '1.0.0';

    /**
     *
     * @var bool
     */
    public bool $hasCpSettings = false;

    /**
     *
     * @var bool
     */
    public bool $hasCpSection = false;

    // Public Methods
    // =========================================================================
    public static function config(): array
    {
        return [
            'components' => [
                'vite' => [
                    'class' => VitePluginService::class,
                    'assetClass' => DonkeytailAsset::class,
                    'useDevServer' => App::env('VITE_PLUGIN_DEVSERVER'),
                    'manifestPath' => '@simplygoodwork/donkeytail/web/dist/.vite/manifest.json',
                    'devServerPublic' => 'https://localhost:3002/',
                    'serverPublic' => App::env('DEFAULT_SITE_URL'),
                    'errorEntry' => 'web/src/main.js',
                    'devServerInternal' => 'https://localhost:3002/',
                    'checkDevServer' => false,
                ],
            ]
        ];
    }

    public function init()
    {
        parent::init();
        self::$plugin = $this;
        
        Craft::setAlias('@simplygoodwork/donkeytail', $this->getBasePath());

        // $this->setComponents([
        //     'vite' => [
        //         'class' => VitePluginService::class,
        //         'assetClass' => DonkeytailAsset::class,
        //         'useDevServer' => App::env('VITE_PLUGIN_DEVSERVER'),
        //         'manifestPath' => '@simplygoodwork/donkeytail/web/dist/.vite/manifest.json',
        //         'devServerPublic' => 'https://localhost:3002/',
        //         'serverPublic' => App::env('DEFAULT_SITE_URL'),
        //         'errorEntry' => 'web/src/main.js',
        //         'devServerInternal' => 'https://localhost:3002/',
        //         'checkDevServer' => false,
        //     ],
        // ]);
        // Register our fields
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = DonkeytailField::class;
            }
        );

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('donkeytail', [
                    'class' => DonkeytailVariable::class,
                    'viteService' => $this->vite,
                ]);
            }
        );

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );

        Event::on(
            Gql::class, 
            Gql::EVENT_REGISTER_GQL_TYPES, 
            function (RegisterGqlTypesEvent $event) {
                $event->types[] = DonkeytailType::class;
            }
        );

    }

    // Protected Methods
    // =========================================================================

}
