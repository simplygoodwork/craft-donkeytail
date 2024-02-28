<?php

/**
 * Donkeytail plugin for Craft CMS 3.x
 *
 * Fieldtype that allows you to quickly and easily content manage points on images. You can use it for locations on a faux map, showcasing multiple products within an image, or even pinning the tail on a donkey.
 *
 * @link      https://simplygoodwork.com
 * @copyright Copyright (c) 2020 Good Work
 */

namespace simplygoodwork\donkeytail\fields;

use simplygoodwork\donkeytail\assetbundles\donkeytail\DonkeytailAsset;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use yii\db\Schema;
use craft\helpers\Html;
use craft\elements\Asset;
use craft\helpers\Json;
use simplygoodwork\donkeytail\models\DonkeytailModel;
use simplygoodwork\donkeytail\gql\DonkeytailType;

/**
 * Donkeytail Field
 * @author    Good Work
 * @package   Donkeytail
 * @since     1.0.0
 */
class Donkeytail extends Field
{
    // Public Properties
    // =========================================================================

    public array $canvasSources = [];

    public string $pinElementType = '';

    public array $entrySources = [];

    public array $assetSources = [];

    public array $userSources = [];

    public array $categorySources = [];

    public array $productSources = [];

    public array $variantSources = [];

    public bool $autoSelect = false;

    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('donkeytail', 'Donkeytail');
    }

    // Public Methods
    // =========================================================================

    /**
     * @return array
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules = array_merge($rules, []);
        return $rules;
    }

    /**
     * @return array|null|string
     */
    public static function dbType(): array|null|string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * @param mixed                 $value   The raw field value
     * @param ElementInterface|null $element
     * @return mixed The prepared field value
     */
    public function normalizeValue(mixed $value, ?ElementInterface $element = null): mixed
    {
        $site = ($element ? $element->getSite() : Craft::$app->getSites()->getCurrentSite());

        if (is_string($value) && !empty($value)) {
            $value = Json::decode($value);
        }

        $value['site'] = $site;

        if($value instanceof DonkeytailModel){
          return $value;
        }

        return new DonkeytailModel($value);
    }

    /**
     * @param mixed $value The raw field value
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     * @return mixed The serialized field value
     */
    public function serializeValue(mixed $value, ?\craft\base\ElementInterface $element = null): mixed
    {
        $_serialized = parent::serializeValue($value, $element);
        unset($_serialized['site']);

        return $_serialized;
    }

    /**
     * @return string|null
     */
    public function getSettingsHtml(): ?string
    {
        $view = Craft::$app->getView();

        // Register our asset bundle
        $view->registerAssetBundle(DonkeytailAsset::class);

        $id = Html::id('donkeytail');
        $namespacedId = $view->namespaceInputId($id);

        $productSources = [];
        if (Craft::$app->plugins->isPluginEnabled('commerce')) {
            $productSources = $this->getSourceOptions('craft\commerce\elements\Product');
            $variantSources = $this->getSourceOptions('craft\commerce\elements\Variant');
        }

        // Render the settings template
        return $view->renderTemplate(
            'donkeytail/_components/fields/Donkeytail_settings',
            [
                'id' => $id,
                'namespacedId' => $namespacedId,
                'field' => $this,
                'pinElementType' => $this->pinElementType,
                'assetSources' => $this->getSourceOptions('craft\elements\Asset'),
                'entrySources' => $this->getSourceOptions('craft\elements\Entry'),
                'userSources' => $this->getSourceOptions('craft\elements\User'),
                'categorySources' => $this->getSourceOptions('craft\elements\Category'),
                'productSources' => $productSources ?? null,
                'variantSources' => $variantSources ?? null,
            ]
        );
    }

    /**
     * @param mixed                 $value
     * @param ElementInterface|null $element
     * @return string The input HTML.
     */
    public function getInputHtml(mixed $value, ?\craft\base\ElementInterface $element = null): string
    {
        $view = Craft::$app->getView();

        // Register our asset bundle
        $view->registerAssetBundle(DonkeytailAsset::class);

        $id = Html::id($this->handle);
        $namespacedId = $view->namespaceInputId($id);
        $site = ($element ? $element->getSite() : Craft::$app->getSites()->getCurrentSite());

        $csrf = Craft::$app->request->csrfToken;
        $view->registerJs("window.csrfToken = '$csrf';", $view::POS_HEAD);
        $view->registerJs("window.dispatchEvent(new CustomEvent('build', { detail: '#$namespacedId-app' }));", $view::POS_END);

        // Set canvas asset element
        $canvasElements = [];

        if ($value['canvasId'] && is_array($value['canvasId'])) {
            $canvasElements = [Craft::$app->getAssets()->getAssetById($value['canvasId'][0])];
        }

        $pinElementType = null;
        $commerceElementType = false;
        if ($pinElementType = $this->pinElementType) {
            if (in_array($pinElementType, ['Product', 'Variant'])) {
                $commerceElementType = true;
                $pinElementType = "craft\\commerce\\elements\\$pinElementType";
            } else {
                $pinElementType = "craft\\elements\\$pinElementType";
            }
        }

        $findPins = true;
        // Ensure Commerce is enabled before trying to find elements for it
        if ($commerceElementType == true && !Craft::$app->plugins->isPluginEnabled('commerce')) {
            $findPins = false;
        }

        // Set pin elements
        $pinElements = [];
        $meta = [];
        if ($findPins == true && $value['pinIds'] && is_array($value['pinIds'])) {
            foreach ($value['pinIds'] as $pinId) {
                $pinElement = Craft::$app->getElements()->getElementById($pinId, $pinElementType, $site->id);
                if ($pinElement) {
                    // If element exists, show it
                    $pinElements[] = $pinElement;

                    // Ensure label for pin element is up to date
                    if ($this->pinElementType === 'User') {
                        $value->meta[$pinElement->id]['label'] = $pinElement->username;
                    } else {
                        $value->meta[$pinElement->id]['label'] = $pinElement->title;
                    }

                    // Only include meta for elements that exist
                    $meta[] = $value->meta[$pinElement->id];
                }
            }
        }

        // Set element sources and use Entry as fallback
        $pinElementSources = $this->pinElementType ? strtolower($this->pinElementType).'Sources' : 'entrySources';

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'donkeytail/_components/fields/Donkeytail_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
                'element' => $element,
                'site' => $site->handle,

                'canvasSourceExists' => count(Craft::$app->getAssets()->findFolders([])),
                'canvasElements' => $canvasElements,
                'canvasElementType' => Asset::class,
                'canvasSources' => $this->canvasSources,

                'pinElementType' => $pinElementType,
                'pinElementSources' => $this->{$pinElementSources} ?: null,
                'pinElements' => $pinElements,

                'meta' => json_encode($meta),
            ]
        );
    }


    /**
     * @inheritdoc
     */
    public function getSourceOptions($elementType = 'craft\elements\Asset'): array
    {
        $sourceOptions = [];

        foreach ($elementType::sources('settings') as $volume) {
            if (!isset($volume['heading'])) {
                $sourceOptions[] = [
                    'label' => Html::encode($volume['label']),
                    'value' => $volume['key']
                ];
            }
        }

        return $sourceOptions;
    }


    /**
     * @inheritdoc
     */
    public function getContentGqlType(): array|\GraphQL\Type\Definition\Type {
        return DonkeytailType::getType();
    }
}
