<?php
namespace Craft;

class DigitalDownloadPlugin extends BasePlugin
{

	public function init()
	{
		parent::init();
		craft()->digitalDownload->settings = $this->getSettings();
	}

	public function getName()
	{
		return 'Digital Download';
	}

	public function getDescription()
	{
		return 'A secure way to provide digital download links to your files.';
	}

	public function getDocumentationUrl()
	{
		return 'https://craftpl.us/plugins/digital-download';
	}

	public function getVersion()
	{
		return '0.9.0';
	}

	public function getSchemaVersion()
	{
		return '0.0.0';
	}

	public function getDeveloper()
	{
		return 'Double Secret Agency';
	}

	public function getDeveloperUrl()
	{
		return 'https://craftpl.us/plugins/digital-download';
		//return 'http://doublesecretagency.com';
	}

	public function getSettingsHtml()
	{
		return craft()->templates->render('digitaldownload/_settings', array(
			'settings' => craft()->digitalDownload->settings
		));
	}

	protected function defineSettings()
	{
		return array(
			'keepDownloadLog' => array(AttributeType::Bool, 'default' => false),
		);
	}

}
