<?php
namespace Craft;

class DigitalDownload_EmailTemplateRecord extends BaseRecord
{

	public function getTableName()
	{
		return 'digitaldownload_emailtemplates';
	}

	protected function defineAttributes()
	{
		return array(
			'html' => array(AttributeType::String, 'column' => ColumnType::Text),
			'to'   => array(AttributeType::String),
			'from' => array(AttributeType::String),
			'cc'   => array(AttributeType::String),
			'bcc'  => array(AttributeType::String),
		);
	}

/*
Email templates can use tokens:

> Thank you for your purchase! Here is your download link:
>
> http://www.mysite.com/download/aE4r9348yf9esfh9843r43tgv
*/

}