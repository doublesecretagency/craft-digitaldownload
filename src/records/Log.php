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

namespace doublesecretagency\digitaldownload\records;

use craft\db\ActiveRecord;
use DateTime;

/**
 * Class Log
 * @since 2.0.0
 *
 * @property int      $id
 * @property int      $tokenId
 * @property int      $assetId
 * @property int      $userId
 * @property string   $ipAddress
 * @property int      $success
 * @property string   $error
 * @property DateTime $dateCreated
 * @property DateTime $dateUpdated
 * @property string   $uid
 */
class Log extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%digitaldownload_log}}';
    }

}
