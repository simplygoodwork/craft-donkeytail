<?php

/**
 * Donkeytail plugin for Craft CMS 3.x
 *
 * Fieldtype that allows you to quickly and easily content manage points on images. You can use it for locations on a faux map, showcasing multiple products within an image, or even pinning the tail on a donkey.
 *
 * @link      https://simplygoodwork.com
 * @copyright Copyright (c) 2020 Good Work
 */

namespace simplygoodwork\donkeytail\assetbundles\donkeytail;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;
use craft\web\assets\vue\VueAsset;
use craft\web\assets\axios\AxiosAsset;

/**
 * DonkeytailAsset AssetBundle
 *
 * AssetBundle represents a collection of asset files, such as CSS, JS, images.
 *
 * Each asset bundle has a unique name that globally identifies it among all asset bundles used in an application.
 * The name is the [fully qualified class name](http://php.net/manual/en/language.namespaces.rules.php)
 * of the class representing it.
 *
 * An asset bundle can depend on other asset bundles. When registering an asset bundle
 * with a view, all its dependent asset bundles will be automatically registered.
 *
 * http://www.yiiframework.com/doc-2.0/guide-structure-assets.html
 *
 * @author    Good Work
 * @package   Donkeytail
 * @since     1.0.0
 */
class DonkeytailAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the bundle.
     */
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = "@simplygoodwork/donkeytail/assetbundles/donkeytail/dist";

        // define the dependencies
        $this->depends = [
            CpAsset::class,
            VueAsset::class,
            AxiosAsset::class
        ];

        // define the relative path to CSS/JS files that should be registered with the page
        // when this asset bundle is registered
        if (getenv('DONKEYTAIL_DEBUG') == 'true') {
            
            $this->js = ['http://localhost:8080/js/chunk-vendors.js', 'http://localhost:8080/js/app.js'];
        } else {
            $this->js = ['js/app.js', 'js/chunk-vendors.js'];
            $this->css = ['css/app.css', 'css/chunk-vendors.css'];
        }

        parent::init();
    }
}
