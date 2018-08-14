<?php
/**
 * Digital Download plugin for Craft CMS
 *
 * Provide secure digital download links to your files.
 *
 * @author    Double Secret Agency
 * @link      https://www.doublesecretagency.com/
 * @copyright Copyright (c) 2016 Double Secret Agency
 */

namespace doublesecretagency\digitaldownload\controllers;

use Craft;
use craft\web\Controller;

use doublesecretagency\digitaldownload\DigitalDownload;

/**
 * Class DownloadController
 * @since 2.0.0
 */
class DownloadController extends Controller
{

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     * @access protected
     */
    protected $allowAnonymous = true;

    /**
     * Follow long path to download file
     */
    public function actionIndex()
    {
        $token = Craft::$app->getRequest()->getQueryParam('u');
        $this->_download($token);
    }

    /**
     * Follow short path to download file.
     *
     * @param array $variables
     */
    public function actionShortPath(string $token = null)
    {
        $this->_download($token);
    }

    /**
     * Initiate the download.
     *
     * @param $token
     */
    private function _download($token)
    {
        DigitalDownload::$plugin->digitalDownload_download->startDownload($token);
    }

}
