<?php
namespace Craft;

class DigitalDownloadService extends BaseApplicationComponent
{

	public $settings;

	public function hash()
	{
		return md5(microtime());
	}

	public function generateAccessKey(AssetFileModel $file, $options = array())
	{
		// Load options
		$ttl          = $this->_setValue($options, 'ttl',         'P14D' );
		$maxDownloads = $this->_setValue($options, 'maxDownloads', 0     );

		// Generate access key
		$accessKey = $this->hash();

		$expires = new DateTime();
		$expires = $expires->add(new DateInterval($ttl));

		$linkRecord = new DigitalDownload_LinkRecord();

		$linkRecord->assetId      = $file->id;
		$linkRecord->accessKey    = $accessKey;
		$linkRecord->expires      = $expires;
		$linkRecord->maxDownloads = $maxDownloads;

		$linkRecord->save();

		return $accessKey;
	}

	public function linkData($accessKey)
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

	public function cleanup()
	{
		$this->disableExpiredLinks();
	}

	public function disableExpiredLinks()
	{
		craft()->db->createCommand()->update(
			'digitaldownload_links',
			array('enabled' => 0),
			'(expires <= NOW()) AND (enabled = 1)'
		);
	}

	/*
	 * Should this method be scrapped?
	 *
	 * Deletes all expired links from database.
	 */
	/*
	public function deleteExpiredLinks()
	{
		craft()->db->createCommand()->delete(
			'digitaldownload_links',
			'expires <= NOW()'
		);
	}
	*/

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

	private function _setValue($options, $key, $default)
	{
		return (array_key_exists($key, $options) ? $options[$key] : $default);
	}

	// ========================================================================= //

	public function actionUrl($accessKey)
	{
		return UrlHelper::getActionUrl(
			'digitalDownload/download',
			array('u' => $accessKey)
		);
	}

	public function downloadUrl($file, $options = array())
	{
		$accessKey = $this->_fileOrKey($file, $options);
		if ($accessKey) {
			return $this->actionUrl($accessKey);
		} else {
			return '[invalid access key]';
		}
	}

	public function downloadLink($file, $options = array())
	{
		$url   = $this->downloadUrl($file, $options);
		$label = $this->_setValue($options, 'label', 'Download');

		return TemplateHelper::getRaw('<a href="'.$url.'">'.$label.'</a>');
	}

	// ========================================================================= //

	// Ensures that we're working with an access key
	private function _fileOrKey($file, $options = array())
	{
		// Parse access key based on what $file is
		if (is_a($file, 'Craft\\AssetFileModel')) {

			// If $file is an asset, generate key
			return $this->generateAccessKey($file, $options);

		} else if (is_string($file)) {

			// If $file is a key, use key
			return $file;

		}

		// Otherwise, $file is invalid
		return false;
	}

}
