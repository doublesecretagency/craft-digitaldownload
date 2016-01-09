<?php
namespace Craft;

class DigitalDownloadController extends BaseController
{

	protected $allowAnonymous = array('actionDownload');

	public function actionDownload()
	{
		$accessKey = craft()->request->getQuery('u');
		$link = craft()->digitalDownload->link($accessKey);
		$asset = $link->asset();
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

}