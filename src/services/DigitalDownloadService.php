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
use craft\helpers\StringHelper;
use craft\helpers\Template;
use craft\helpers\UrlHelper;
use doublesecretagency\digitaldownload\DigitalDownload;
use doublesecretagency\digitaldownload\models\Link;
use doublesecretagency\digitaldownload\records\Token as TokenRecord;
use Exception;
use Twig\Markup;

/**
 * Class DigitalDownloadService
 * @since 2.0.0
 *
 * @property Token $digitalDownload_token
 */
class DigitalDownloadService extends Component
{

    /**
     * Generate a random string to be used as a token.
     *
     * @return string Randomly generated string.
     */
    public function hash(): string
    {
        return StringHelper::randomString();
    }

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
        return DigitalDownload::$plugin->digitalDownload_token->createToken($file, $options);
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
    public function url($token, array $options = []): string
    {
        // Ensures we're working with a proper token
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $token = $this->_tokenOrFile($token, $options);

        // If token does not exist
        if (!$token) {
            return '[invalid token]';
        }

        // Get short path
        $shortPath = DigitalDownload::$plugin->getSettings()->shortPath;
        $shortPath = trim($shortPath, ' /');

        // If short path exists, use it
        if ($shortPath) {
            /** @noinspection PhpUnhandledExceptionInspection */
            return UrlHelper::siteUrl($shortPath.'/'.$token);
        }

        // Use long path
        return UrlHelper::actionUrl('digital-download/download', ['u' => $token]);
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
     * @deprecated in 2.1
     */
    public function link($token, $options = [], $label = 'Download'): Markup
    {
        // Deprecation
        Craft::$app->getDeprecator()->log('DigitalDownloadService::link', 'DigitalDownloadService::link() has been deprecated. Use DigitalDownloadService::url() to generate the URL, and compose the link manually instead.');

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
     * @return Link|false
     */
    public function getLinkData(string $token): Link
    {
        // Get token record
        $tokenRecord = TokenRecord::findOne([
            'token' => $token
        ]);

        // If no token record, bail
        if (!$tokenRecord) {
            return false;
        }

        // Return link model
        return new Link($tokenRecord);
    }

    /**
     * Disable all tokens which have expired.
     */
    public function disableExpiredLinks()
    {
        try {

            Craft::$app->getDb()->createCommand()
                ->update(
                    '{{%digitaldownload_tokens}}',
                    ['enabled' => 0],
                    [
                        'and',
                        ['enabled' => 1],
                        ['<=', 'expires', date('Y-m-d H:i:s')]
                    ]
                )
                ->execute();

        } catch (Exception $e) {

            // Something went wrong, do nothing

        }
    }

    /**
     * Disable all tokens which have expired.
     * Alias of `disableExpiredLinks()`.
     *
     * @see disableExpiredLinks()
     */
    public function cleanup()
    {
        $this->disableExpiredLinks();
    }

    // =========================================================================

    /**
     * Ensures that we're working with a proper token
     *
     * @param Asset|string $token Existing token, or file to be tokenized.
     * @param array $options Configuration of download token.
     * @return string|false
     * @throws Exception
     */
    private function _tokenOrFile($token, $options = [])
    {
        // If $token is a token, use the token
        if (is_string($token)) {
            return $token;
        }

        // If $token is an asset, create a token
        if (is_a($token, Asset::class)) {
            return $this->createToken($token, $options);
        }

        // $token is invalid
        return false;
    }

}
