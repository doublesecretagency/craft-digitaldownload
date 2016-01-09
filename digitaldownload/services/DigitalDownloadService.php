<?php
namespace Craft;

class DigitalDownloadService extends BaseApplicationComponent
{

	public $settings;

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

		$linkRecord = new DigitalDownload_LinkRecord();

		$linkRecord->assetId   = $file->id;
		$linkRecord->accessKey = $accessKey;
		$linkRecord->expires   = $expires;

		$linkRecord->save();

		return $accessKey;
	}

	public function link($accessKey)
	{
		$linkRecord = $this->_linkRecord($accessKey);
		return DigitalDownload_LinkModel::populateModel($linkRecord);
	}

	public function trackDownload($accessKey)
	{
		$linkRecord = $this->_linkRecord($accessKey);
		$linkRecord->totalDownloads++;
		$linkRecord->lastDownloaded = new DateTime();
		$this->_logDownload($linkRecord);
		return $linkRecord->save();
	}

	public function pruneLinks()
	{
		craft()->db->createCommand()->delete(
			'digitaldownload_links',
			'expires <= NOW()'
		);
	}

	// ========================================================================= //

	private function _linkRecord($accessKey)
	{
		return DigitalDownload_LinkRecord::model()->findByAttributes(array(
			'accessKey' => $accessKey
		));
	}

	private function _logDownload($linkRecord)
	{
		if ($this->settings['keepDownloadLog']) {
			$log = new DigitalDownload_DownloadLogRecord();
			$log->linkId     = $linkRecord->id;
			$log->downloaded = new DateTime();
			$log->save();
		}
	}

}
