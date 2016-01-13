<?php
namespace Craft;

class DigitalDownloadVariable
{

	public function generateAccessKey(AssetFileModel $file, $options = array())
	{
		return craft()->digitalDownload->generateAccessKey($file, $options);
	}

	// ========================================================================= //

	public function downloadUrl($file, $options = array())
	{
		return craft()->digitalDownload->downloadUrl($file, $options);
	}

	public function downloadLink($file, $options = array())
	{
		return craft()->digitalDownload->downloadLink($file, $options);
	}

}
