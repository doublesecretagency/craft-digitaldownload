<?php
/**
 * Digital Download plugin for Craft CMS
 *
 * Provide secure digital download links to your files.
 *
 * @author    Double Secret Agency
 * @link      https://www.doublesecretagency.com/
 * @copyright Copyright (c) 2016 Double Secret Agency
 */

namespace doublesecretagency\digitaldownload\services;

use Craft;
use craft\base\Component;
use craft\elements\Asset;

use doublesecretagency\digitaldownload\DigitalDownload;
use doublesecretagency\digitaldownload\records\Token as TokenRecord;

/**
 * Class Token
 * @since 2.0.0
 */
class Token extends Component
{

    public function createToken(Asset $file, $options = [])
    {
        // Generate token
        $token = DigitalDownload::$plugin->digitalDownload->hash();

        // Load options
        $ttl          = $this->_setValue($options, 'expires',      'P14D');
        $requireUser  = $this->_setValue($options, 'requireUser',  null  );
        $maxDownloads = $this->_setValue($options, 'maxDownloads', 0     );

        // Set expiration date
        $expires = new \DateTime();
        $expires = $expires->add(new \DateInterval($ttl));

        // Create new token record
        $linkRecord = new TokenRecord();

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
