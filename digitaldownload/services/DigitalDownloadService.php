<?php
namespace Craft;

class DigitalDownloadService extends BaseApplicationComponent
{

	public $settings;

	public function hash()
	{
		return StringHelper::randomString();
	}

	public function createToken(AssetFileModel $file, $options = array())
	{
		return craft()->digitalDownload_token->createToken($file, $options);
	}

	// ========================================================================= //

	// Generates a URL to download the file
	public function url($token, $options = array())
	{
		$token = $this->_tokenOrFile($token, $options);
		// If token exists
		if ($token) {
			// Get short path
			$shortPath = craft()->digitalDownload->settings->shortPath;
			$shortPath = trim($shortPath, ' /');
			// If short path exists
			if ($shortPath) {
				// Use short path
				return UrlHelper::getSiteUrl(
					$shortPath.'/'.$token
				);
			} else {
				// Use long path
				return UrlHelper::getActionUrl(
					'digitalDownload/download',
					array('u' => $token)
				);
			}
		} else {
			// Output error message
			return '[invalid token]';
		}
	}

	// Generates a full HTML <a> tag
	public function link($token, $options = array(), $label = 'Download')
	{
		// If options param is skipped
		if (is_string($options)) {
			$label = $options;
			$options = array();
		}
		// Set URL
		$url = $this->url($token, $options);
		// Return HTML
		return TemplateHelper::getRaw('<a href="'.$url.'">'.$label.'</a>');
	}

	// ========================================================================= //

	public function getLinkData($token)
	{
		// Get token record
		$tokenRecord = DigitalDownload_TokenRecord::model()->findByAttributes(array(
			'token' => $token
		));
		// Return link model
		return DigitalDownload_LinkModel::populateModel($tokenRecord);
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
