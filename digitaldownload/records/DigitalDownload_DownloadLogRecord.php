<?php
namespace Craft;

class DigitalDownload_DownloadLogRecord extends BaseRecord
{

	public function getTableName()
	{
		return 'digitaldownload_downloadlog';
	}

	protected function defineAttributes()
	{
		return array(
			'downloaded' => array(AttributeType::DateTime, 'default' => new DateTime),
			// 'userId' => AttributeType::Number,
			// 'ipAddress' => AttributeType::String,
		);
	}

	public function defineRelations()
	{
		return array(
			'link' => array(static::BELONGS_TO, 'DigitalDownload_TokenRecord', 'required' => true, 'onDelete' => static::CASCADE),
		);
	}

}