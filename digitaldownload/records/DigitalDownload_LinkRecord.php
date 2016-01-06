<?php
namespace Craft;

class DigitalDownload_LinkRecord extends BaseRecord
{

	public function getTableName()
	{
		return 'digitaldownload_links';
	}

	protected function defineAttributes()
	{
		return array(
			'accessKey'      => array(AttributeType::String),
			'expires'        => array(AttributeType::DateTime, 'default' => null),
			'totalDownloads' => array(AttributeType::Number,   'default' => 0),
			'lastDownloaded' => array(AttributeType::DateTime, 'default' => null),
		);
	}

	public function defineRelations()
	{
		return array(
			'asset' => array(static::BELONGS_TO, 'AssetFileRecord', 'required' => true, 'onDelete' => static::CASCADE),
		);
	}

}