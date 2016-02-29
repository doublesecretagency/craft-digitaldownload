<?php
namespace Craft;

class DigitalDownload_TokenService extends BaseApplicationComponent
{

	public function createToken(AssetFileModel $file, $options = array())
	{
		// Load options
		$expires      = $this->_setValue($options, 'expires',     'P14D');
		$maxDownloads = $this->_setValue($options, 'maxDownloads', 0    );

		// Generate token
		$token = craft()->digitalDownload->hash();

		// Set expiration date
		$ttl = new DateTime();
		$ttl = $ttl->add(new DateInterval($expires));

		// Create new token record
		$linkRecord = new DigitalDownload_TokenRecord();

		// Configure token record
		$linkRecord->assetId      = $file->id;
		$linkRecord->token        = $token;
		$linkRecord->expires      = $ttl;
		$linkRecord->maxDownloads = $maxDownloads;

		// Save token record
		$linkRecord->save();

		// Return token string
		return $token;
	}

	// ========================================================================= //

	// Set option value (or default)
	private function _setValue($options, $key, $default)
	{
		return (array_key_exists($key, $options) ? $options[$key] : $default);
	}

}
