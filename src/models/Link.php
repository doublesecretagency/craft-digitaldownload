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

namespace doublesecretagency\digitaldownload\models;

use Craft;
use craft\base\Model;

use doublesecretagency\digitaldownload\DigitalDownload;

/**
 * Class Link
 * @since 2.0.0
 */
class Link extends Model
{

    /** @var int  $id  ID of link. */
    public $id;

    /** @var int  $assetId  ID of related asset. */
    public $assetId;

    /** @var string  $token  Unique download token. */
    public $token;

    /** @var bool  $enabled  Whether token is enabled. */
    public $enabled;

    /** @var \DateTime  $expires  Expiration date/time of download token. */
    public $expires;

    /** @var string  $requireUser  Optionally require a user or group. */
    public $requireUser;

    /** @var int  $maxDownloads  Maximum number of allowed downloads. */
    public $maxDownloads;

    /** @var int  $totalDownloads  Total number of times downloaded. */
    public $totalDownloads;

    /** @var \DateTime  $lastDownloaded  Date/time of the most recent download. */
    public $lastDownloaded;

    /** @var string  $error  Optional error message if download not permitted. */
    public $error;

    /** @var \DateTime  $dateCreated  Date/time link was created. */
    public $dateCreated;

    /** @var \DateTime  $dateUpdated  Date/time link was updated. */
    public $dateUpdated;

    /** @var string  $uid  Unique row ID. */
    public $uid;

    /**
     * Use the link's token as the string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->token;
    }

    // Get the related asset
    public function asset()
    {
        return Craft::$app->assets->getAssetById($this->assetId);
    }

    // Generates a URL to download the file
    public function url($options = [])
    {
        return DigitalDownload::$plugin->digitalDownload->url($this->token, $options);
    }

    // Generates a full HTML <a> tag
    public function html($options = [])
    {
        return DigitalDownload::$plugin->digitalDownload->link($this->token, $options);
    }

}
