<?php
namespace Craft;

class DigitalDownload_DownloadService extends BaseApplicationComponent
{

	private $_currentUserId     = null;
	private $_currentUserGroups = null;

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
			// Log & output error message
			DigitalDownloadPlugin::log("Unable to download with token {$token}. {$link->error}", LogLevel::Warning);
			echo craft()->templates->renderString($link->error);
			craft()->end();
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
			$currentUser = craft()->userSession->getUser();
			$log = new DigitalDownload_LogRecord();
			$log->tokenId   = $tokenRecord->id;
			$log->assetId   = $tokenRecord->assetId;
			$log->userId    = ($currentUser ? $currentUser->id : null);
			$log->ipAddress = $_SERVER['REMOTE_ADDR'];
		}

		// If no errors
		if (!$link->error) {

			// Increment token record
			$tokenRecord->totalDownloads++;
			$tokenRecord->lastDownloaded = new DateTime();
			$tokenRecord->save();

			// Log success
			if ($logging) {
				$log->success = true;
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
		if (!$this->_isEnabled($link)) {
			$link->error = 'Link is disabled.';
			return false;
		} else if (!$this->_isUnexpired($link)) {
			$link->error = 'Link has expired.';
			return false;
		} else if (!$this->_isUnderMaxDownloads($link)) {
			$link->error = 'Link has reached max downloads.';
			return false;
		} else if (!$this->_isAuthorizedUser($link)) {
			$link->error = 'User is not authorized.';
			return false;
		}

		// Passed all checks
		return true;
	}

	// Check whether link is enabled
	private function _isEnabled($link)
	{
		return $link->enabled;
	}

	// Check whether link has not yet expired
	private function _isUnexpired($link)
	{
		$now = new DateTime;
		return ($now->getTimestamp() < $link->expires->getTimestamp());
	}

	// Check whether link is under maximum downloads
	private function _isUnderMaxDownloads($link)
	{
		if ($link->maxDownloads) {
			return ($link->totalDownloads < $link->maxDownloads);
		} else {
			return true;
		}
	}

	// Check whether user is authorized
	private function _isAuthorizedUser($link)
	{
		// Load current user info
		$this->_loadUserData();

		// Decode user requirements
		$requirement = json_decode($link->requireUser);

		// All access granted (including anonymous)
		if (null === $requirement) {
			return true;

		// Must be logged in
		} else if ('*' === $requirement) {
			return (bool) $this->_currentUserId;

		// Must be this user
		} else if (is_numeric($requirement)) {
			return $this->_isCurrentUser($requirement);

		// Must be in this user group
		} else if (is_string($requirement)) {
			return $this->_isCurrentUserInGroup($requirement);

		// Multiple users or groups
		} else if (is_array($requirement)) {
			foreach ($requirement as $userOrGroup) {

				// Must be this user
				if (is_numeric($userOrGroup)) {
					if ($this->_isCurrentUser($userOrGroup)) {
						return true;
					}

				// Must be in this user group
				} else if (is_string($userOrGroup)) {
					if ($this->_isCurrentUserInGroup($userOrGroup)) {
						return true;
					}
				}

			}
		}

		// Failed the test
		return false;

	}

	// Check whether user is current user
	private function _isCurrentUser($userId)
	{
		return ($this->_currentUserId == $userId);
	}

	// Check whether current user is in a group
	private function _isCurrentUserInGroup($group)
	{
		return in_array($group, $this->_currentUserGroups);
	}

	// Load data for current user
	private function _loadUserData()
	{
		$user = craft()->userSession->getUser();
		if ($user && !$this->_currentUserId) {
			$this->_currentUserId = $user->id;
			$this->_currentUserGroups = array();
			foreach ($user->groups as $group) {
				$this->_currentUserGroups[] = $group->handle;
			}
		}
	}

}
