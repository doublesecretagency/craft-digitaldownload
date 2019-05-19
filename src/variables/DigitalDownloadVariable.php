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

namespace doublesecretagency\digitaldownload\variables;

use craft\elements\Asset;
use doublesecretagency\digitaldownload\DigitalDownload;
use doublesecretagency\digitaldownload\models\Link;

/**
 * Class DigitalDownloadVariable
 * @since 2.0.0
 */
class DigitalDownloadVariable
{

    /**
     * Create a token for a file.
     *
     * @param Asset $file File to be represented by token.
     * @param array $options Configuration of download token.
     * @return string
     */
    public function createToken(Asset $file, $options = []): string
    {
        return DigitalDownload::$plugin->digitalDownload->createToken($file, $options);
    }

    // =========================================================================

    /**
     * Generates a URL to download the file.
     *
     * @param Asset|string $token Existing token, or file to be tokenized.
     * @param array $options Configuration of download token.
     * @return string
     */
    public function url($token, array $options = []): string
    {
        return DigitalDownload::$plugin->digitalDownload->url($token, $options);
    }

    /**
     * Generates a full HTML <a> tag.
     *
     * @param Asset|string $token Existing token, or file to be tokenized.
     * @param array|string $options Configuration of download token,
     *                              or the link label if using an existing token.
     * @param string $label Optional label of download link.
     * @return string
     * @throws \Exception
     * @deprecated in 2.1
     */
    public function link($token, $options = [], string $label = 'Download')
    {
        // Deprecation
        \Craft::$app->getDeprecator()->log('DigitalDownloadService::link', 'craft.digitalDownload.link() has been deprecated. Use craft.digitalDownload.url() to generate the URL, and compose the link manually instead.');

        return DigitalDownload::$plugin->digitalDownload->link($token, $options, $label);
    }

    // =========================================================================

    /**
     * Get the link data from an existing token.
     *
     * @param string $token Existing token.
     * @return Link|false
     */
    public function getLinkData(string $token): Link
    {
        return DigitalDownload::$plugin->digitalDownload->getLinkData($token);
    }

}
