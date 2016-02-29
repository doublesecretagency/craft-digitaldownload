<?php
namespace Craft;

class DigitalDownload_TokenService extends BaseApplicationComponent
{

	public function createToken(AssetFileModel $file, $options = array())
	{
		// Generate token
		$token = craft()->digitalDownload->hash();

		// Load options
		$ttl          = $this->_setValue($options, 'expires',     'P14D');
		$requireUser  = $this->_setValue($options, 'requireUser',  null );
		$maxDownloads = $this->_setValue($options, 'maxDownloads', 0    );

		// Set expiration date
		$expires = new DateTime();
		$expires = $expires->add(new DateInterval($ttl));

		// Create new token record
		$linkRecord = new DigitalDownload_TokenRecord();

		// Configure token record
		$linkRecord->assetId      = $file->id;
		$linkRecord->token        = $token;
		$linkRecord->expires      = $expires;
		$linkRecord->requireUser  = json_encode($requireUser);
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
