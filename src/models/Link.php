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
    public $id;

    /**
     * @var int ID of related asset.
     */
    public $assetId;

    /**
     * @var string Unique download token.
     */
    public $token;

    /**
     * @var bool Whether token is enabled.
     */
    public $enabled;

    /**
     * @var DateTime Expiration date/time of download token.
     */
    public $expires;

    /**
     * @var string Optionally require a user or group.
     */
    public $requireUser;

    /**
     * @var int Maximum number of allowed downloads.
     */
    public $maxDownloads;

    /**
     * @var int Total number of times downloaded.
     */
    public $totalDownloads;

    /**
     * @var DateTime Date/time of the most recent download.
     */
    public $lastDownloaded;

    /**
     * @var string Optional error message if download not permitted.
     */
    public $error;

    /**
     * @var DateTime Date/time link was created.
     */
    public $dateCreated;

    /**
     * @var DateTime Date/time link was updated.
     */
    public $dateUpdated;

    /**
     * @var string Unique row ID.
     */
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

    /**
     * Get the related asset.
     *
     * @return Asset|null
     */
    public function asset()
    {
        return Craft::$app->assets->getAssetById($this->assetId);
    }

    /**
     * Generates a URL to download the file.
     *
     * @param array $options Configuration of download token.
     * @return string
     * @throws Exception
     */
    public function url($options = []): string
    {
        return DigitalDownload::$plugin->digitalDownload->url($this->token, $options);
    }

    /**
     * Generates a full HTML <a> tag.
     *
     * @param array $options Configuration of download token.
     * @param string $label Optional label of download link.
     * @return Markup
     * @throws Exception
     */
    public function html($options = [], $label = 'Download'): Markup
    {
        // Generate a URL to download the file
        $url = $this->url($options);

        // Return the HTML link
        return Template::raw("<a href=\"{$url}\">{$label}</a>");
    }

}
