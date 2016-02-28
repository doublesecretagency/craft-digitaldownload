<?php
namespace Craft;

class DigitalDownload_TokenRecord extends BaseRecord
{

	public function getTableName()
	{
		return 'digitaldownload_tokens';
	}

	protected function defineAttributes()
	{
		return array(
			'token'          => array(AttributeType::String),
			'enabled'        => array(AttributeType::Bool,     'default' => true),
			'expires'        => array(AttributeType::DateTime, 'default' => null),
			'requireUser'    => array(AttributeType::String,   'default' => null),
			'maxDownloads'   => array(AttributeType::Number,   'default' => 0),
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

	public function defineIndexes()
	{
		return array(
			array('columns' => array('token'), 'unique' => true),
		);
	}

}