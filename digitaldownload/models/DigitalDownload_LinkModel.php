<?php
namespace Craft;

class DigitalDownload_LinkModel extends BaseModel
{

	public function __toString()
	{
		return $this->token;
	}

	protected function defineAttributes()
	{
		return array(
			'id'             => AttributeType::Number,
			'assetId'        => AttributeType::Number,
			'token'          => AttributeType::String,
			'enabled'        => AttributeType::Bool,
			'expires'        => AttributeType::DateTime,
			'maxDownloads'   => AttributeType::Number,
			'totalDownloads' => AttributeType::Number,
			'lastDownloaded' => AttributeType::DateTime,
			'error'          => AttributeType::String,
		);
	}

	// Get the related asset
	public function asset()
	{
		return craft()->assets->getFileById($this->assetId);
	}

	// Generates a URL to download the file
	public function url($options = array())
	{
		return craft()->digitalDownload->url($this->token, $options);
	}

	// Generates a full HTML <a> tag
	public function html($options = array())
	{
		return craft()->digitalDownload->link($this->token, $options);
	}

}