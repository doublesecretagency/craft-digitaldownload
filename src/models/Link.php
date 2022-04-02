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
use craft\elements\Asset;
use craft\helpers\Template;
use DateTime;
use doublesecretagency\digitaldownload\DigitalDownload;
use Exception;
use Twig\Markup;

/**
 * Class Link
 * @since 2.0.0
 */
class Link extends Model
{

    /**
     * @var int ID of link.
     */
    public int $id;

    /**
     * @var int ID of related asset.
     */
    public int $assetId;

    /**
     * @var string Unique download token.
     */
    public string $token;

    /**
     * @var string Optionally append or replace download headers.
     */
    public string $headers;

    /**
     * @var bool Whether token is enabled.
     */
    public bool $enabled;

    /**
     * @var DateTime Expiration date/time of download token.
     */
    public DateTime $expires;

    /**
     * @var string Optionally require a user or group.
     */
    public string $requireUser;

    /**
     * @var int Maximum number of allowed downloads.
     */
    public int $maxDownloads;

    /**
     * @var int Total number of times downloaded.
     */
    public int $totalDownloads;

    /**
     * @var DateTime|null Date/time of the most recent download.
     */
    public ?DateTime $lastDownloaded = null;

    /**
     * @var string|null Optional error message if download not permitted.
     */
    public ?string $error = null;

    /**
     * @var DateTime Date/time link was created.
     */
    public DateTime $dateCreated;

    /**
     * @var DateTime Date/time link was updated.
     */
    public DateTime $dateUpdated;

    /**
     * @var string Unique row ID.
     */
    public string $uid;

    /**
     * Use the link's token as the string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->token;
    }

    /**
     * Get the related asset.
     *
     * @return Asset|null
     */
    public function asset(): ?Asset
    {
        return Craft::$app->getAssets()->getAssetById($this->assetId);
    }

    /**
     * Get the download URL of specified token.
     *
     * @return string
     * @throws Exception
     */
    public function url(): string
    {
        return DigitalDownload::$plugin->digitalDownload->url($this->token);
    }

    /**
     * Generates a full HTML <a> tag.
     *
     * @param string $label Optional label of download link.
     * @return Markup
     * @throws Exception
     */
    public function html(string $label = 'Download'): Markup
    {
        // Generate a URL to download the file
        $url = $this->url();

        // Return the HTML link
        return Template::raw("<a href=\"{$url}\">{$label}</a>");
    }

}
