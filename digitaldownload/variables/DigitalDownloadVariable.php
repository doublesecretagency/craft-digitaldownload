<?php
namespace Craft;

class DigitalDownloadVariable
{

	public function generateAccessKey(AssetFileModel $file, $options = array())
	{
		return craft()->digitalDownload->generateAccessKey($file, $options);
	}

	public function actionUrl($accessKey)
	{
		return UrlHelper::getActionUrl(
			'digitalDownload/download',
			array('u' => $accessKey)
		);
	}

	public function downloadUrl($file, $options = array())
	{
		$accessKey = $this->_deduceKey($file, $options);
		if ($accessKey) {
			return $this->actionUrl($accessKey);
		} else {
			return '[invalid access key]';
		}
	}

	public function downloadLink($file, $label = 'Download')
	{
		$url = $this->downloadUrl($file);
		$link = '<a href="'.$url.'">'.$label.'</a>';
		return TemplateHelper::getRaw($link);
	}

	// ========================================================================= //

	private function _deduceKey($file, $options = array())
	{
		// Parse access key based on what $file is
		if (is_a($file, 'Craft\\AssetFileModel')) {

			// If $file is an asset, generate key
			return $this->generateAccessKey($file, $options);

		} else if (is_string($file)) {

			// If $file is a key, use key
			return $file;

		} else {

			// Otherwise, $file is invalid
			return false;

		}
	}

}
