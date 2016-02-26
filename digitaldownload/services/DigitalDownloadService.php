<?php
namespace Craft;

class DigitalDownloadService extends BaseApplicationComponent
{

	public $settings;

	public function hash()
	{
		return md5(microtime());
	}

	public function createToken(AssetFileModel $file, $options = array())
	{
		// Load options
		$ttl          = $this->_setValue($options, 'ttl',         'P14D' );
		$maxDownloads = $this->_setValue($options, 'maxDownloads', 0     );

		// Generate token
		$token = $this->hash();

		$expires = new DateTime();
		$expires = $expires->add(new DateInterval($ttl));

		$linkRecord = new DigitalDownload_TokenRecord();

		$linkRecord->assetId      = $file->id;
		$linkRecord->token        = $token;
		$linkRecord->expires      = $expires;
		$linkRecord->maxDownloads = $maxDownloads;

		$linkRecord->save();

		return $token;
	}

	public function linkData($token)
	{
		$linkRecord = $this->_linkRecord($token);
		return DigitalDownload_TokenModel::populateModel($linkRecord);
	}

	public function trackDownload($token)
	{
		$linkRecord = $this->_linkRecord($token);
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

	private function _linkRecord($token)
	{
		return DigitalDownload_TokenRecord::model()->findByAttributes(array(
			'token' => $token
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

	// Generates a URL to download the file
	public function url($token, $options = array())
	{
		$token = $this->_tokenOrFile($token, $options);
		if ($token) {
			return UrlHelper::getActionUrl(
				'digitalDownload/download',
				array('u' => $token)
			);
		} else {
			return '[invalid token]';
		}
	}

	// Generates a full HTML <a> tag
	public function link($token, $options = array())
	{
		$url   = $this->url($token, $options);
		$label = $this->_setValue($options, 'label', 'Download');

		return TemplateHelper::getRaw('<a href="'.$url.'">'.$label.'</a>');
	}

	// ========================================================================= //

	// Ensures that we're working with a proper token
	private function _tokenOrFile($token, $options = array())
	{
		// If $token is a token, use the token
		if (is_string($token)) {
			return $token;

		// If $token is an asset, create a token
		} else if (is_a($token, 'Craft\\AssetFileModel')) {
			return $this->createToken($token, $options);

		// Else, $token is invalid
		} else {
			return false;

		}
	}

}
