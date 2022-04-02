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

use Craft;
use craft\elements\Asset;
use craft\helpers\Template;
use doublesecretagency\digitaldownload\DigitalDownload;
use doublesecretagency\digitaldownload\models\Link;
use Exception;
use Twig\Markup;

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
     * @throws Exception
     */
    public function createToken(Asset $file, array $options = []): string
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
     * @throws Exception
     */
    public function url(Asset|string $token, array $options = []): string
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
     * @return Markup
     * @throws Exception
     * @deprecated in 2.1.0. Use [[url()]] to generate the URL, and compose the link manually instead.
     */
    public function link(Asset|string $token, array|string $options = [], string $label = 'Download'): Markup
    {
        // Deprecation warning
        Craft::$app->getDeprecator()->log('DigitalDownloadService::link', 'craft.digitalDownload.link() has been deprecated. Use craft.digitalDownload.url() to generate the URL, and compose the link manually instead.');

        // If options param is skipped
        if (is_string($options)) {
            $label = $options;
            $options = [];
        }

        // Generate a URL to download the file
        $url = $this->url($token, $options);

        // Return the HTML link
        return Template::raw("<a href=\"{$url}\">{$label}</a>");
    }

    // =========================================================================

    /**
     * Get the link data from an existing token.
     *
     * @param string $token Existing token.
     * @return Link|null
     */
    public function getLinkData(string $token): ?Link
    {
        return DigitalDownload::$plugin->digitalDownload->getLinkData($token);
    }

}
