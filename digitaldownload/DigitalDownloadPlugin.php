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
		return '';
	}

	public function getDocumentationUrl()
	{
		return '';
	}

	public function getVersion()
	{
		return '0.5.0';
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
			// 'maxStarsAvailable' => array(AttributeType::Number, 'default' => 5),
			// 'requireLogin'      => array(AttributeType::Bool,   'default' => true),
			// 'allowHalfStars'    => array(AttributeType::Bool,   'default' => true),
			// 'allowRatingChange' => array(AttributeType::Bool,   'default' => true),
			// 'allowFontAwesome'  => array(AttributeType::Bool,   'default' => true),
			'keepDownloadLog'     => array(AttributeType::Bool,   'default' => false),
		);
	}

}
