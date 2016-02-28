<?php
namespace Craft;

class DigitalDownload_LogRecord extends BaseRecord
{

	public function getTableName()
	{
		return 'digitaldownload_log';
	}

	protected function defineAttributes()
	{
		return array(
			'tokenId'    => AttributeType::Number,
			'assetId'    => AttributeType::Number,
			'downloaded' => array(AttributeType::DateTime, 'default' => null),
			'success'    => array(AttributeType::Bool,     'default' => false),
			'error'      => array(AttributeType::String,   'default' => ''),
			// 'userId' => AttributeType::Number,
			// 'ipAddress' => AttributeType::String,
		);
	}

}