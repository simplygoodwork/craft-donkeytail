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

use Craft;
use craft\base\ElementInterface;
use craft\base\Model;
use craft\services\Elements;

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
 *
 * @property-read null|object $canvas
 * @property-read array $pins
 * @property-read null $pinsElementType
 */
class DonkeytailModel extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Some model attribute
     *
     * @var string
     */
    public $site = "";

    /**
     * Some model attribute
     *
     * @var string
     */
    public $canvasId = "";

    /**
     * Some model attribute
     *
     * @var array
     */
    public $pinIds = [];

    /**
     * Some model attribute
     *
     * @var array
     */
    public $meta = [];

    // Private Properties
    // =========================================================================

    private ?ElementInterface $_owner = null;

    private ?string $_fieldHandle = null;

    private ?array $_eagerLoadedElementsById = null;

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
    public function rules(): array
    {
        return [
            ['canvasId', 'string'],
            ['pinIds', 'array'],
            ['meta', 'array'],
        ];
    }

    /**
     * Set the owner element and field handle for lazy eager-loading access.
     */
    public function setOwner(ElementInterface $element, string $fieldHandle): void
    {
        $this->_owner = $element;
        $this->_fieldHandle = $fieldHandle;
    }

    /**
     * Get the canvas asset
     *
     * @return null|object
     */
    public function getCanvas()
    {
        $result = null;

        if (isset($this->canvasId) && $this->canvasId) {
            $canvasId = is_array($this->canvasId) ? $this->canvasId[0] : $this->canvasId;

            $eagerLoaded = $this->_getEagerLoadedElementsById();
            if ($eagerLoaded !== null && isset($eagerLoaded[$canvasId])) {
                return $eagerLoaded[$canvasId];
            }

            $result = Craft::$app->getAssets()->getAssetById($canvasId);
        }

        return $result;
    }

    /**
     * Get the URL to the canvas asset
     *
     * @param null $transform
     *
     * @return null|string
     */
    public function getCanvasUrl($transform = null)
    {
        $result = '';

        if ($canvas = $this->getCanvas()) {
            $result = $canvas->getUrl($transform);
        }

        return $result;
    }

    public function getPinsElementType() {
        if (!$this->pinIds) return null;

        return (new Elements)->getElementTypeById($this->pinIds[0]);
    }

    public function getPins()
    {
        $pins = [];

        $elementTypeClass = $this->getPinsElementType();
        if (!$elementTypeClass) return $pins;

        // Check eager-loaded elements (works when pins are Assets)
        $eagerLoaded = $this->_getEagerLoadedElementsById();
        $queryAll = null;

        if ($eagerLoaded !== null && !empty($this->pinIds)) {
            $allFound = true;
            $eagerPins = [];
            foreach ($this->pinIds as $pinId) {
                if (isset($eagerLoaded[(int)$pinId])) {
                    $eagerPins[] = $eagerLoaded[(int)$pinId];
                } else {
                    $allFound = false;
                    break;
                }
            }
            if ($allFound) {
                $queryAll = $eagerPins;
            }
        }

        // Fall back to query
        if ($queryAll === null) {
            $query = $elementTypeClass::find();
            $criteria = [
                'id' => $this->pinIds,
                'site' => $this->site->handle,
                'fixedOrder' => true
            ];
            Craft::configure($query, $criteria);
            $queryAll = $query->all();
        }

        foreach ($queryAll as $key => $element) {
            if (!isset($this->meta[$element->id])) {
                continue;
            }
            $pinMeta = $this->meta[$element->id];
            $pin = new PinModel();
            $pin->element = $element;
            $pin->x = $pinMeta['x'];
            $pin->y = $pinMeta['y'];
            array_push($pins, $pin);
        }

        return $pins;
    }

    // Private Methods
    // =========================================================================

    /**
     * Lazily resolve eager-loaded elements indexed by ID.
     */
    private function _getEagerLoadedElementsById(): ?array
    {
        if ($this->_eagerLoadedElementsById !== null) {
            return $this->_eagerLoadedElementsById;
        }

        if ($this->_owner && $this->_fieldHandle) {
            $collection = $this->_owner->getEagerLoadedElements($this->_fieldHandle);
            if ($collection !== null) {
                $this->_eagerLoadedElementsById = [];
                foreach ($collection as $element) {
                    $this->_eagerLoadedElementsById[$element->id] = $element;
                }
                return $this->_eagerLoadedElementsById;
            }
        }

        return null;
    }
}
