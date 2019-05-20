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

use craft\base\Component;
use craft\elements\Asset;
use craft\helpers\Json;
use DateInterval;
use DateTime;
use doublesecretagency\digitaldownload\DigitalDownload;
use doublesecretagency\digitaldownload\records\Token as TokenRecord;
use Exception;

/**
 * Class Token
 * @since 2.0.0
 */
class Token extends Component
{

    /**
     * Create a token for a file.
     *
     * @param Asset $file File to be represented by token.
     * @param array $options Configuration of download token.
     * @return string
     * @throws Exception
     */
    public function createToken(Asset $file, array $options = []): string
    {
        // Generate token
        $token = DigitalDownload::$plugin->digitalDownload->hash();

        // Load options
        $ttl          = ($options['expires']      ?? 'P14D');
        $requireUser  = ($options['requireUser']  ?? null);
        $maxDownloads = ($options['maxDownloads'] ?? 0);
        $headers      = ($options['headers']      ?? []);

        // Set expiration date
        $expires = new DateTime();
        $expires = $expires->add(new DateInterval($ttl));

        // Create new token record
        $linkRecord = new TokenRecord();

        // Configure token record
        $linkRecord->assetId      = $file->id;
        $linkRecord->token        = $token;
        $linkRecord->headers      = Json::encode($headers, JSON_FORCE_OBJECT);
        $linkRecord->expires      = $expires;
        $linkRecord->requireUser  = Json::encode($requireUser);
        $linkRecord->maxDownloads = $maxDownloads;

        // Save token record
        $linkRecord->save();

        // Return token string
        return $token;
    }

}
