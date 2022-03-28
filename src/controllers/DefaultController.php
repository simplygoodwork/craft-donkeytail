<?php
/**
 * Donkeytail plugin for Craft CMS 3.x
 *
 * .
 *
 * @link      https://simplygoodwork.com
 * @copyright Copyright (c) 2020 Good Work
 */

namespace simplygoodwork\donkeytail\controllers;

use simplygoodwork\donkeytail\Donkeytail;

use Craft;
use craft\web\Controller;

/**
 * @author    Good Work
 * @package   Donkeytail
 * @since     1.0.0
 */
class DefaultController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected array|bool|int $allowAnonymous = ['index', 'get-asset'];

    // Public Methods
    // =========================================================================

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $result = 'Welcome to the DefaultController actionIndex() method';

        return $result;
    }

    /**
     * @return mixed
     */
    public function actionGetAsset()
    {
        $assetId = Craft::$app->getRequest()->getRequiredParam('assetId');
        $assets = Craft::$app->getAssets();

        if (!$asset = $assets->getAssetById($assetId)) {
            // throw new NotFoundHttpException('Asset not found.');
        }
        return $asset->getUrl();

        // return $this->asJson([

        // ]);
    }
}
