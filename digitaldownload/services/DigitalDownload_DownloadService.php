<?php
namespace Craft;

class DigitalDownload_DownloadService extends BaseApplicationComponent
{

	public function startDownload($token)
	{
		// Get link data
		$link = craft()->digitalDownload->getLinkData($token);

		// Check if download is authorized
		$authorized = $this->authorized($link);

		// Track download attempt
		$this->trackDownload($link);

		// If authorized
		if ($authorized) {
			// Download file
			$this->_outputFile($link);
		} else {
			DigitalDownloadPlugin::log("Unable to download with token {$token}. {$link->error}", LogLevel::Warning);
		}
	}

	public function trackDownload($link)
	{
		// Get token record
		$tokenRecord = DigitalDownload_TokenRecord::model()->findByAttributes(array(
			'token' => $link->token
		));

		// Whether or not to log
		$logging = craft()->digitalDownload->settings['keepDownloadLog'];

		// Initialize log record
		if ($logging) {
			$log = new DigitalDownload_LogRecord();
			$log->tokenId = $tokenRecord->id;
			$log->assetId = $tokenRecord->assetId;
		}

		// If no errors
		if (!$link->error) {

			// Increment token record
			$tokenRecord->totalDownloads++;
			$tokenRecord->lastDownloaded = new DateTime();
			$tokenRecord->save();

			// Log success
			if ($logging) {
				$log->success    = true;
				$log->downloaded = new DateTime();
				$log->save();
			}

		} else {

			// Log failure
			if ($logging) {
				$log->success = false;
				$log->error   = $link->error;
				$log->save();
			}

		}
	}

	// ========================================================================= //

	private function _outputFile($link)
	{
		$asset = $link->asset();
		if ($asset) {
			header("Content-type: application/octet-stream");
			header("Content-disposition: attachment; filename=".$asset->filename);
			echo file_get_contents($asset->url);
			exit;
		}
	}

	// ========================================================================= //

	public function authorized($link)
	{
		if ($this->_isExpired($link)) {
			$link->error = 'Link has expired.';
			return false;
		} else if ($this->_isOverMaxDownloads($link)) {
			$link->error = 'Link has reached max downloads.';
			return false;
		} else if ($this->_isNotAuthorizedUser($link)) {
			$link->error = 'User is not authorized.';
			return false;
		}

		// Passed all checks
		return true;
	}

	// Check whether link has expired
	private function _isExpired($link)
	{
		return false;
	}

	// Check whether link has reached maximum downloads
	private function _isOverMaxDownloads($link)
	{
		return false;
	}

	// Check whether user is authorized
	private function _isNotAuthorizedUser($link)
	{
		return false;
	}

}
