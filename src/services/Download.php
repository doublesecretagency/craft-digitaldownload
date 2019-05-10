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

use doublesecretagency\digitaldownload\DigitalDownload;
use doublesecretagency\digitaldownload\records\Log as LogRecord;
use doublesecretagency\digitaldownload\records\Token as TokenRecord;

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
        // Get link data
        $link = DigitalDownload::$plugin->digitalDownload->getLinkData($token);

        // Check if download is authorized
        $authorized = $this->authorized($link);

        // Track download attempt
        $this->trackDownload($link);

        // If authorized
        if ($authorized) {
            // Download file
            $this->_outputFile($link);
        } else {
            // Log & output error message
            $error = (string) $link->error;
//            DigitalDownloadPlugin::log("Unable to download with token {$token}. {$error}", LogLevel::Warning);
            echo Craft::$app->getView()->renderString($error);
            Craft::$app->end();
        }
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
        $asset = $link->asset();
        if ($asset) {

            header('Content-type: application/octet-stream');
            header('Content-disposition: attachment; filename='.$asset->filename);
            header('Content-Length: '.$asset->size);

            $assetFilePath = $this->_getAssetFilePath($asset);
            readfile($assetFilePath);
            exit;
        }
    }

    private function _getAssetFilePath($asset)
    {
        $volumePath = $asset->getVolume()->settings['path'];
        $folderPath = $asset->getFolder()->path.'/';

        return Craft::getAlias($volumePath).$folderPath.$asset->filename;
    }

    // ========================================================================

    public function authorized($link)
    {
        if (!$this->_isEnabled($link)) {
            $link->error = 'Link is disabled.';
            return false;
        }
        if (!$this->_isUnexpired($link)) {
            $link->error = 'Link has expired.';
            return false;
        }
        if (!$this->_isUnderMaxDownloads($link)) {
            $link->error = 'Link has reached max downloads.';
            return false;
        }
        if (!$this->_isAuthorizedUser($link)) {
            $link->error = 'User is not authorized.';
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
