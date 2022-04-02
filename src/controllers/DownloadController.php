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
use yii\base\InvalidConfigException;
use yii\web\HttpException;

/**
 * Class DownloadController
 * @since 2.0.0
 */
class DownloadController extends Controller
{

    /**
     * @inheritdoc
     */
    protected array|bool|int $allowAnonymous = true;

    /**
     * Follow long path to download file.
     *
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function actionIndex(): void
    {
        $token = Craft::$app->getRequest()->getQueryParam('u');
        $this->_download($token);
    }

    /**
     * Follow short path to download file.
     *
     * @param string|null $token Token representing file to be downloaded.
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function actionShortPath(?string $token = null): void
    {
        $this->_download($token);
    }

    /**
     * Initiate the download.
     *
     * @param string|null $token Token representing file to be downloaded.
     * @throws HttpException
     * @throws InvalidConfigException
     */
    private function _download(?string $token = null): void
    {
        DigitalDownload::$plugin->digitalDownload_download->startDownload($token);
    }

}
