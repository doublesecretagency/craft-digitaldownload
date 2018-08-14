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

use yii\db\Exception;

use Craft;
use craft\base\Component;
use craft\elements\Asset;
use craft\helpers\StringHelper;
use craft\helpers\Template;
use craft\helpers\UrlHelper;

use doublesecretagency\digitaldownload\DigitalDownload;
use doublesecretagency\digitaldownload\models\Link;
use doublesecretagency\digitaldownload\records\Token as TokenRecord;

/**
 * Class DigitalDownloadService
 * @since 2.0.0
 */
class DigitalDownloadService extends Component
{

    public $settings;

    public function hash()
    {
        return StringHelper::randomString();
    }

    public function createToken(Asset $file, $options = [])
    {
        return DigitalDownload::$plugin->digitalDownload_token->createToken($file, $options);
    }

    // ========================================================================= //

    // Generates a URL to download the file
    public function url($token, $options = [])
    {
        $token = $this->_tokenOrFile($token, $options);
        // If token exists
        if ($token) {
            // Get short path
            $shortPath = DigitalDownload::$plugin->getSettings()->shortPath;
            $shortPath = trim($shortPath, ' /');
            // If short path exists, use it
            if ($shortPath) {
                return UrlHelper::siteUrl($shortPath.'/'.$token);
            }
            // Use long path
            return UrlHelper::actionUrl('digital-download/download', ['u' => $token]);
        }
        // Output error message
        return '[invalid token]';
    }

    // Generates a full HTML <a> tag
    public function link($token, $options = [], $label = 'Download')
    {
        // If options param is skipped
        if (is_string($options)) {
            $label = $options;
            $options = [];
        }
        // Set URL
        $url = $this->url($token, $options);
        // Return HTML
        return Template::raw('<a href="'.$url.'">'.$label.'</a>');
    }

    // ========================================================================= //

    public function getLinkData($token)
    {
        // Get token record
        $tokenRecord = TokenRecord::findOne([
            'token' => $token
        ]);
        // Return link model
        return new Link($tokenRecord);
    }

    public function cleanup()
    {
        $this->disableExpiredLinks();
    }

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
        }
    }

    // ========================================================================= //

    // Ensures that we're working with a proper token
    private function _tokenOrFile($token, $options = [])
    {
        // If $token is a token, use the token
        if (is_string($token)) {
            return $token;
        }
        // If $token is an asset, create a token
        if (is_a($token, 'craft\\elements\\Asset')) {
            return $this->createToken($token, $options);
        }
        // $token is invalid
        return false;
    }

}
