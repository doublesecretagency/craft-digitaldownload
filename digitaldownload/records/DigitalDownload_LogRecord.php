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
			'userId'     => array(AttributeType::Number, 'default' => null),
			'ipAddress'  => array(AttributeType::String, 'default' => null),
			'success'    => array(AttributeType::Bool,   'default' => false),
			'error'      => array(AttributeType::String, 'default' => ''),
		);
	}

}