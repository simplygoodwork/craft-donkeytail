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

// use simplygoodwork\donkeytail\Donkeytail;
use simplygoodwork\donkeytail\assetbundles\donkeytail\DonkeytailAsset;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use yii\db\Schema;
use craft\helpers\Html;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\elements\Category;
use craft\helpers\Json;
use simplygoodwork\donkeytail\models\DonkeytailModel;
use simplygoodwork\donkeytail\gql\DonkeytailType;

/**
 * Donkeytail Field
 *
 * Whenever someone creates a new field in Craft, they must specify what
 * type of field it is. The system comes with a handful of field types baked in,
 * and we’ve made it extremely easy for plugins to add new ones.
 *
 * https://craftcms.com/docs/plugins/field-types
 *
 * @author    Good Work
 * @package   Donkeytail
 * @since     1.0.0
 */
class Donkeytail extends Field
{
    // Public Properties
    // =========================================================================

    public $canvasSources = [];

    public $pinElementType = [];

    public $entrySources = [];

    public $assetSources = [];

    public $categorySources = [];

    public $productSources = [];

    public $variantSources = [];

    public $autoSelect = false;

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
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules = array_merge($rules, []);
        return $rules;
    }

    /**
     * Returns the column type that this field should get within the content table.
     *
     * This method will only be called if [[hasContentColumn()]] returns true.
     *
     * @return string The column type. [[\yii\db\QueryBuilder::getColumnType()]] will be called
     * to convert the give column type to the physical one. For example, `string` will be converted
     * as `varchar(255)` and `string(100)` becomes `varchar(100)`. `not null` will automatically be
     * appended as well.
     * @see \yii\db\QueryBuilder::getColumnType()
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * Normalizes the field’s value for use.
     *
     * This method is called when the field’s value is first accessed from the element. For example, the first time
     * `entry.myFieldHandle` is called from a template, or right before [[getInputHtml()]] is called. Whatever
     * this method returns is what `entry.myFieldHandle` will likewise return, and what [[getInputHtml()]]’s and
     * [[serializeValue()]]’s $value arguments will be set to.
     *
     * @param mixed                 $value   The raw field value
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     *
     * @return mixed The prepared field value
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        if (is_string($value) && !empty($value)) {
            $value = Json::decode($value);
        }

        $model = new DonkeytailModel($value);
        return $model;
    }

    /**
     * Prepares the field’s value to be stored somewhere, like the content table or JSON-encoded in an entry revision table.
     *
     * Data types that are JSON-encodable are safe (arrays, integers, strings, booleans, etc).
     * Whatever this returns should be something [[normalizeValue()]] can handle.
     *
     * @param mixed $value The raw field value
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     * @return mixed The serialized field value
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        return parent::serializeValue($value, $element);
    }

    /**
     * Returns the component’s settings HTML.
     *
     * An extremely simple implementation would be to directly return some HTML:
     *
     * ```php
     * return '<textarea name="foo">'.$this->getSettings()->foo.'</textarea>';
     * ```
     *
     * For more complex settings, you might prefer to create a template, and render it via
     * [[\craft\web\View::renderTemplate()]]. For example, the following code would render a template loacated at
     * craft/plugins/myplugin/templates/_settings.html, passing the settings to it:
     *
     * ```php
     * return Craft::$app->getView()->renderTemplate('myplugin/_settings', [
     *     'settings' => $this->getSettings()
     * ]);
     * ```
     *
     * If you need to tie any JavaScript code to your settings, it’s important to know that any `name=` and `id=`
     * attributes within the returned HTML will probably get [[\craft\web\View::namespaceInputs() namespaced]],
     * however your JavaScript code will be left untouched.
     *
     * For example, if getSettingsHtml() returns the following HTML:
     *
     * ```html
     * <textarea id="foo" name="foo"></textarea>
     *
     * <script type="text/javascript">
     *     var textarea = document.getElementById('foo');
     * </script>
     * ```
     *
     * …then it might actually look like this before getting output to the browser:
     *
     * ```html
     * <textarea id="namespace-foo" name="namespace[foo]"></textarea>
     *
     * <script type="text/javascript">
     *     var textarea = document.getElementById('foo');
     * </script>
     * ```
     *
     * As you can see, that JavaScript code will not be able to find the textarea, because the textarea’s `id=`
     * attribute was changed from `foo` to `namespace-foo`.
     *
     * Before you start adding `namespace-` to the beginning of your element ID selectors, keep in mind that the actual
     * namespace is going to change depending on the context. Often they are randomly generated. So it’s not quite
     * that simple.
     *
     * Thankfully, [[\craft\web\View]] service provides a couple handy methods that can help you deal
     * with this:
     *
     * - [[\craft\web\View::namespaceInputId()]] will give you the namespaced version of a given ID.
     * - [[\craft\web\View::namespaceInputName()]] will give you the namespaced version of a given input name.
     * - [[\craft\web\View::formatInputId()]] will format an input name to look more like an ID attribute value.
     *
     * So here’s what a getSettingsHtml() method that includes field-targeting JavaScript code might look like:
     *
     * ```php
     * public function getSettingsHtml()
     * {
     *     // Come up with an ID value for 'foo'
     *     $id = Craft::$app->getView()->formatInputId('foo');
     *
     *     // Figure out what that ID is going to be namespaced into
     *     $namespacedId = Craft::$app->getView()->namespaceInputId($id);
     *
     *     // Render and return the input template
     *     return Craft::$app->getView()->renderTemplate('myplugin/_fieldinput', [
     *         'id'           => $id,
     *         'namespacedId' => $namespacedId,
     *         'settings'     => $this->getSettings()
     *     ]);
     * }
     * ```
     *
     * And the _settings.html template might look like this:
     *
     * ```twig
     * <textarea id="{{ id }}" name="foo">{{ settings.foo }}</textarea>
     *
     * <script type="text/javascript">
     *     var textarea = document.getElementById('{{ namespacedId }}');
     * </script>
     * ```
     *
     * The same principles also apply if you’re including your JavaScript code with
     * [[\craft\web\View::registerJs()]].
     *
     * @return string|null
     */
    public function getSettingsHtml()
    {
        $view = Craft::$app->getView();

        // Register our asset bundle
        $view->registerAssetBundle(DonkeytailAsset::class);

        $id = $view->formatInputId('donkeytail');
        $namespacedId = $view->namespaceInputId($id);

        $productSources = [];
        if (Craft::$app->plugins->isPluginEnabled('commerce')) {
            $productSources = $this->getSourceOptions('craft\commerce\elements\Product');
            $variantSources = $this->getSourceOptions('craft\commerce\elements\Variant');
        }

        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
            'donkeytail/_components/fields/Donkeytail_settings',
            [
                'id' => $id,
                'namespacedId' => $namespacedId,
                'field' => $this,
                'pinElementType' => $this->pinElementType,
                'assetSources' => $this->getSourceOptions('craft\elements\Asset'),
                'entrySources' => $this->getSourceOptions('craft\elements\Entry'),
                'categorySources' => $this->getSourceOptions('craft\elements\Category'),
                'productSources' => $productSources ?? null,
                'variantSources' => $variantSources ?? null,
            ]
        );
    }

    /**
     * Returns the field’s input HTML.
     *
     * An extremely simple implementation would be to directly return some HTML:
     *
     * ```php
     * return '<textarea name="'.$name.'">'.$value.'</textarea>';
     * ```
     *
     * For more complex inputs, you might prefer to create a template, and render it via
     * [[\craft\web\View::renderTemplate()]]. For example, the following code would render a template located at
     * craft/plugins/myplugin/templates/_fieldinput.html, passing the $name and $value variables to it:
     *
     * ```php
     * return Craft::$app->getView()->renderTemplate('myplugin/_fieldinput', [
     *     'name'  => $name,
     *     'value' => $value
     * ]);
     * ```
     *
     * If you need to tie any JavaScript code to your input, it’s important to know that any `name=` and `id=`
     * attributes within the returned HTML will probably get [[\craft\web\View::namespaceInputs() namespaced]],
     * however your JavaScript code will be left untouched.
     *
     * For example, if getInputHtml() returns the following HTML:
     *
     * ```html
     * <textarea id="foo" name="foo"></textarea>
     *
     * <script type="text/javascript">
     *     var textarea = document.getElementById('foo');
     * </script>
     * ```
     *
     * …then it might actually look like this before getting output to the browser:
     *
     * ```html
     * <textarea id="namespace-foo" name="namespace[foo]"></textarea>
     *
     * <script type="text/javascript">
     *     var textarea = document.getElementById('foo');
     * </script>
     * ```
     *
     * As you can see, that JavaScript code will not be able to find the textarea, because the textarea’s `id=`
     * attribute was changed from `foo` to `namespace-foo`.
     *
     * Before you start adding `namespace-` to the beginning of your element ID selectors, keep in mind that the actual
     * namespace is going to change depending on the context. Often they are randomly generated. So it’s not quite
     * that simple.
     *
     * Thankfully, [[\craft\web\View]] provides a couple handy methods that can help you deal with this:
     *
     * - [[\craft\web\View::namespaceInputId()]] will give you the namespaced version of a given ID.
     * - [[\craft\web\View::namespaceInputName()]] will give you the namespaced version of a given input name.
     * - [[\craft\web\View::formatInputId()]] will format an input name to look more like an ID attribute value.
     *
     * So here’s what a getInputHtml() method that includes field-targeting JavaScript code might look like:
     *
     * ```php
     * public function getInputHtml($value, $element)
     * {
     *     // Come up with an ID value based on $name
     *     $id = Craft::$app->getView()->formatInputId($name);
     *
     *     // Figure out what that ID is going to be namespaced into
     *     $namespacedId = Craft::$app->getView()->namespaceInputId($id);
     *
     *     // Render and return the input template
     *     return Craft::$app->getView()->renderTemplate('myplugin/_fieldinput', [
     *         'name'         => $name,
     *         'id'           => $id,
     *         'namespacedId' => $namespacedId,
     *         'value'        => $value
     *     ]);
     * }
     * ```
     *
     * And the _fieldinput.html template might look like this:
     *
     * ```twig
     * <textarea id="{{ id }}" name="{{ name }}">{{ value }}</textarea>
     *
     * <script type="text/javascript">
     *     var textarea = document.getElementById('{{ namespacedId }}');
     * </script>
     * ```
     *
     * The same principles also apply if you’re including your JavaScript code with
     * [[\craft\web\View::registerJs()]].
     *
     * @param mixed                 $value           The field’s value. This will either be the [[normalizeValue() normalized value]],
     *                                               raw POST data (i.e. if there was a validation error), or null
     * @param ElementInterface|null $element         The element the field is associated with, if there is one
     *
     * @return string The input HTML.
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $view = Craft::$app->getView();

        // Register our asset bundle
        $view->registerAssetBundle(DonkeytailAsset::class);

        $id = $view->formatInputId($this->handle);
        $namespacedId = $view->namespaceInputId($id);

        $csrf = Craft::$app->request->csrfToken;
        $view->registerJs("window.csrfToken = '$csrf';", $view::POS_HEAD);
        $view->registerJs("window.dispatchEvent(new CustomEvent('build', { detail: '#$namespacedId-app' }));", $view::POS_END);

        // Set asset elements
        $canvasElements = [];

        if ($value['canvasId'] && is_array($value['canvasId'])) {
            $canvasElements = [Craft::$app->getAssets()->getAssetById($value['canvasId'][0])];
        }

        $pinElementType = null;
        if ($pinElementType = $this->pinElementType) {
            if (in_array($pinElementType, ['Product', 'Variant'])) {
                $pinElementType = "craft\\commerce\\elements\\$pinElementType";
            } else {
                $pinElementType = "craft\\elements\\$pinElementType";
            }
        }

        // Set entry elements
        $pinElements = [];
        $meta = [];
        if ($value['pinIds'] && is_array($value['pinIds'])) {
            foreach ($value['pinIds'] as $pinId) {
                $pinElement = Craft::$app->getElements()->getElementById($pinId, $pinElementType);
                if ($pinElement) {
                    // If entry exists, show it
                    array_push($pinElements, $pinElement);
                    
                    // Ensure label for pin entry is up to date
                    $value->meta[$pinElement->id]['label'] = $pinElement->title;

                    // Only include meta for entries that exist
                    array_push($meta, $value->meta[$pinElement->id]);
                }
            }
        }

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

                'canvasSourceExists' => count(Craft::$app->getAssets()->findFolders()),
                'canvasElements' => $canvasElements,
                'canvasElementType' => Asset::class,
                'canvasSources' => $this->canvasSources,

                'pinElementType' => $pinElementType,
                'pinElementSources' => $this->{$pinElementSources} ? $this->{$pinElementSources} : null,
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
    public function getContentGqlType() {
        return DonkeytailType::getType();
    }
}
