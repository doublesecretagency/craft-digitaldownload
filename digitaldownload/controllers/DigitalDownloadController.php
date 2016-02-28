<?php
namespace Craft;

class DigitalDownloadController extends BaseController
{

	protected $allowAnonymous = array('actionDownload','actionCleanup');

	public function actionDownload()
	{
		$token = craft()->request->getQuery('u');
		craft()->digitalDownload_download->startDownload($token);
	}

	/* Should this action even exist? */
	/*
	public function actionCleanup()
	{
		craft()->digitalDownload->cleanup();
	}
	*/

}