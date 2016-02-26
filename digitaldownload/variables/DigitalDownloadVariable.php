<?php
namespace Craft;

class DigitalDownloadVariable
{

	// Create a token for a file
	public function createToken(AssetFileModel $file, $options = array())
	{
		return craft()->digitalDownload->createToken($file, $options);
	}

	// ========================================================================= //

	// Generates a URL to download the file
	public function url($token, $options = array())
	{
		return craft()->digitalDownload->url($token, $options);
	}

	// Generates a full HTML <a> tag
	public function link($token, $options = array())
	{
		return craft()->digitalDownload->link($token, $options);
	}

}
