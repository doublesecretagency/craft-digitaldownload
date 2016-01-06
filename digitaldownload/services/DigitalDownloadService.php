<?php
namespace Craft;

class DigitalDownloadService extends BaseApplicationComponent
{

	public function hash()
	{
		return md5(microtime());
	}

	public function generateAccessKey(AssetFileModel $file, $ttl = 'P15D')
	{
		// Generate access key
		$accessKey = $this->hash();

		$expires = new DateTime();
		$expires = $expires->add(new DateInterval($ttl));

		$link = new DigitalDownload_LinkRecord();

		$link->assetId   = $file->id;
		$link->accessKey = $accessKey;
		$link->expires   = $expires;

		$link->save();

		return $accessKey;
	}

	public function link($accessKey)
	{
		$linkRecord = DigitalDownload_LinkRecord::model()->findByAttributes(array(
			'accessKey' => $accessKey
		));
		return DigitalDownload_LinkModel::populateModel($linkRecord);
	}

}
