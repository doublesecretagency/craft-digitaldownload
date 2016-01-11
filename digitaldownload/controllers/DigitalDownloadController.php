<?php
namespace Craft;

class DigitalDownloadController extends BaseController
{

	protected $allowAnonymous = array('actionDownload','actionCleanup');

	public function actionDownload()
	{
		$accessKey = craft()->request->getQuery('u');
		$linkData = craft()->digitalDownload->linkData($accessKey);
		$asset = $linkData->asset();
		craft()->digitalDownload->trackDownload($accessKey);
		$this->_outputFile($asset);
	}

	private function _outputFile($asset)
	{
		header("Content-type: application/octet-stream");
		header("Content-disposition: attachment; filename=".$asset->filename);
		echo file_get_contents($asset->url);
		exit;
	}

	/* Should this action even exist? */
	/*
	public function actionCleanup()
	{
		craft()->digitalDownload->cleanup();
	}
	*/

}