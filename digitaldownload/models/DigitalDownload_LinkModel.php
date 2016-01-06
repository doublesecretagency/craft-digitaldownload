<?php
namespace Craft;

class DigitalDownload_LinkModel extends BaseModel
{

	public function __toString()
	{
		return $this->accessKey;
	}

	protected function defineAttributes()
	{
		return array(
			'id'             => AttributeType::Number,
			'assetId'        => AttributeType::Number,
			'accessKey'      => AttributeType::String,
			'expires'        => AttributeType::DateTime,
			'totalDownloads' => AttributeType::Number,
			'lastDownloaded' => AttributeType::DateTime,
		);
	}

	/**
	 * Get related asset
	 *
	 * @return AssetFileModel
	 */
	public function asset()
	{
		return craft()->assets->getFileById($this->assetId);
	}

}