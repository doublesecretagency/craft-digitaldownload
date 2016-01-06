<?php
namespace Craft;

class DigitalDownloadVariable
{

	public function generateAccessKey(AssetFileModel $file, $ttl = 'P15D')
	{
		return craft()->digitalDownload->generateAccessKey($file, $ttl);
	}

	public function link($accessKey, $label = 'Download')
	{
		$url = '/actions/digitalDownload/download?u='.$accessKey;
		$link = '<a href="'.$url.'">'.$label.'</a>';
		return TemplateHelper::getRaw($link);
	}

}
