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
use craft\helpers\App;
use craft\volumes\Local;
use doublesecretagency\digitaldownload\DigitalDownload;
use doublesecretagency\digitaldownload\records\Log as LogRecord;
use doublesecretagency\digitaldownload\records\Token as TokenRecord;
use yii\web\HttpException;

/**
 * Class Download
 * @since 2.0.0
 */
class Download extends Component
{

    private $_currentUserId;
    private $_currentUserGroups;

    public function startDownload($token)
    {
        // If no token, throw error message
        if (!$token) {
            throw new HttpException(403, 'No download token provided.');
        }

        // Get link data
        $link = DigitalDownload::$plugin->digitalDownload->getLinkData($token);

        // Check if download is authorized
        $authorized = $this->authorized($link);

        // Track download attempt
        $this->trackDownload($link);

        // If authorized, attempt file download
        if ($authorized) {
            $this->_outputFile($link);
        }

        // If something went wrong with the download
        if (!$link->error) {
            $link->error = 'Unknown error when downloading file.';
        }

        // Something went wrong, throw error message
        $error = (string) $link->error;
//      DigitalDownloadPlugin::log("Unable to download with token {$token}. {$error}", LogLevel::Warning);
        throw new HttpException(403, $error);
    }

    public function trackDownload($link)
    {
        // Get token record
        $tokenRecord = TokenRecord::findOne([
            'token' => $link->token
        ]);

        // If no token record, bail
        if (!$tokenRecord) {
            return false;
        }

        // Whether or not to log
        $logging = DigitalDownload::$plugin->getSettings()->keepDownloadLog;

        // Initialize log record
        if ($logging) {
            $currentUser = Craft::$app->user->getIdentity();
            $log = new LogRecord();
            $log->tokenId   = $tokenRecord->id;
            $log->assetId   = $tokenRecord->assetId;
            $log->userId    = ($currentUser ? $currentUser->id : null);
            $log->ipAddress = $_SERVER['REMOTE_ADDR'];
        }

        // If no errors
        if (!$link->error) {

            // Increment token record
            $tokenRecord->totalDownloads++;
            $tokenRecord->lastDownloaded = new \DateTime();
            $tokenRecord->save();

            // Log success
            if ($logging) {
                $log->success = true;
                $log->save();
            }

        } else {

            // Log failure
            if ($logging) {
                $log->success = false;
                $log->error   = $link->error;
                $log->save();
            }

        }
    }

    // ========================================================================

    private function _outputFile($link)
    {
        // Get asset of link
        $asset = $link->asset();

        // If no asset, bail
        if (!$asset) {
            $link->error = 'Link is missing an associated asset.';
            return;
        }

        // Determine volume type
        if (get_class($asset->getVolume()) == Local::class) {

            // Get asset path info
            $volumePath = $asset->getVolume()->settings['path'];
            $folderPath = $asset->getFolder()->path.'/';

            // Set path for local file
            $assetFilePath = Craft::getAlias($volumePath).$folderPath.$asset->filename;

        } else {

            // If no public URL, bail
            if (!$asset->url) {
                $link->error = 'Cloud assets require a public URL.';
                return;
            }

            // Set path for remote file
            $assetFilePath = $asset->url;

        }

        // Start file download
        $this->_downloadFile($asset, $assetFilePath);
    }

    private function _downloadFile(Asset $asset, $filePath)
    {
        // Unlimited PHP memory
        App::maxPowerCaptain();

        // Prevent timeouts
        set_time_limit(0);

        // Set file headers
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$asset->filename);
        header('Content-Length: '.$asset->size);

        // Start with a clean slate
        flush();

        // Open the remote file
        $file = fopen($filePath, 'rb');

        // Read out the remote file in small chunks
        $chunkSize = (1024 * 8);
        while (!feof($file)) {

            // One chunk at a time
            echo fread($file, $chunkSize);

            // Flush to prevent overflow
            ob_flush();
            flush();

        }

        // Close file
        fclose($file);

        // Finish up
        ob_end_flush();
        exit;
    }

    // ========================================================================

    public function authorized($link)
    {
        if (!$this->_isEnabled($link)) {
            $link->error = 'Download link is disabled.';
            return false;
        }
        if (!$this->_isUnexpired($link)) {
            $link->error = 'Download link has expired.';
            return false;
        }
        if (!$this->_isUnderMaxDownloads($link)) {
            $link->error = 'Maximum downloads have been reached.';
            return false;
        }
        if (!$this->_isAuthorizedUser($link)) {
            $link->error = 'User is not authorized to download file.';
            return false;
        }
        // Passed all checks
        return true;
    }

    // Check whether link is enabled
    private function _isEnabled($link)
    {
        return $link->enabled;
    }

    // Check whether link has not yet expired
    private function _isUnexpired($link)
    {
        // Current timestamp
        $current = new \DateTime();
        $now = (int) $current->format('U');

        // Expiration timestamp
        $expires = new \DateTime($link->expires, new \DateTimeZone('UTC'));
        $end = (int) $expires->format('U');

        // Whether link has expired
        return ($now < $end);
    }

    // Check whether link is under maximum downloads
    private function _isUnderMaxDownloads($link)
    {
        if ($link->maxDownloads) {
            return ($link->totalDownloads < $link->maxDownloads);
        }
        return true;
    }

    // ========================================================================

    // Check whether user is authorized
    private function _isAuthorizedUser($link)
    {
        // Load current user info
        $this->_loadUserData();

        // Decode user requirements
        $requirement = json_decode($link->requireUser);

        // All access granted (including anonymous)
        if (null === $requirement) {
            return true;
        }
        // Must be logged in
        if ('*' === $requirement) {
            return (bool) $this->_currentUserId;
        }
        // Must be this user
        if (is_numeric($requirement)) {
            return $this->_isCurrentUser($requirement);
        }
        // Must be in this user group
        if (is_string($requirement)) {
            return $this->_isCurrentUserInGroup($requirement);
        }
        // Multiple users or groups
        if (is_array($requirement)) {
            foreach ($requirement as $userOrGroup) {

                // Must be this user
                if (is_numeric($userOrGroup)) {
                    if ($this->_isCurrentUser($userOrGroup)) {
                        return true;
                    }

                    // Must be in this user group
                } else if (is_string($userOrGroup)) {
                    if ($this->_isCurrentUserInGroup($userOrGroup)) {
                        return true;
                    }
                }

            }
        }

        // Failed the test
        return false;

    }

    // Check whether user is current user
    private function _isCurrentUser($userId)
    {
        return ($this->_currentUserId == $userId);
    }

    // Check whether current user is in a group
    private function _isCurrentUserInGroup($group)
    {
        return in_array($group, $this->_currentUserGroups);
    }

    // Load data for current user
    private function _loadUserData()
    {
        $this->_currentUserGroups = [];
        $user = Craft::$app->user->getIdentity();
        if ($user && !$this->_currentUserId) {
            $this->_currentUserId = $user->id;
            foreach ($user->groups as $group) {
                $this->_currentUserGroups[] = $group->handle;
            }
        }
    }

}
