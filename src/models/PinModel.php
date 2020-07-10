<?php

/**
 * Donkeytail plugin for Craft CMS 3.x
 *
 * Fieldtype that allows you to quickly and easily content manage points on images. You can use it for locations on a faux map, showcasing multiple products within an image, or even pinning the tail on a donkey.
 *
 * @link      https://simplygoodwork.com
 * @copyright Copyright (c) 2020 Good Work
 */

namespace simplygoodwork\donkeytail\models;

use simplygoodwork\donkeytail\Donkeytail;

use Craft;
use craft\base\Model;
use craft\elements\Entry;

/**
 * DonkeytailModel Model
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Good Work
 * @package   Donkeytail
 * @since     1.0.0
 */
class PinModel extends Model
{
    // Public Properties
    // =========================================================================


    public $entry = null;

    public $x = '';

    public $y = '';

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
        return [
            ['entry', 'object'],
            ['x', 'string'],
            ['y', 'string'],
        ];
    }

    public function  getStyle() {
        return "left: $this->x%; top: $this->y%;";
    }

    public function  getLeft() {
        return "$this->x%";
    }

    public function  getTop() {
        return "$this->y%";
    }
}
