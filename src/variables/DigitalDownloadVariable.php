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

/**
 * Class DigitalDownloadVariable
 * @since 2.0.0
 */
class DigitalDownloadVariable
{

    // Create a token for a file
    public function createToken(Asset $file, $options = [])
    {
        return DigitalDownload::$plugin->digitalDownload->createToken($file, $options);
    }

    // ========================================================================

    // Generates a URL to download the file
    public function url($token, $options = [])
    {
        return DigitalDownload::$plugin->digitalDownload->url($token, $options);
    }

    // Generates a full HTML <a> tag
    public function link($token, $options = [], $label = 'Download')
    {
        return DigitalDownload::$plugin->digitalDownload->link($token, $options, $label);
    }

}
