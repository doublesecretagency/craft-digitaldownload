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
use craft\records\Asset;
use yii\db\ActiveQueryInterface;

/**
 * Class Token
 * @since 2.0.0
 *
 * @property int       $id
 * @property int       $assetId
 * @property string    $token
 * @property int       $enabled
 * @property \DateTime $expires
 * @property string    $requireUser
 * @property int       $maxDownloads
 * @property int       $totalDownloads
 * @property \DateTime $lastDownloaded
 * @property \DateTime $dateCreated
 * @property \DateTime $dateUpdated
 * @property string    $uid
 *
 * @property ActiveQueryInterface $asset File represented by token.
 */
class Token extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%digitaldownload_tokens}}';
    }

    /**
     * Returns the downloadable asset.
     *
     * @return ActiveQueryInterface The relational query object.
     */
    public function getAsset(): ActiveQueryInterface
    {
        return $this->hasOne(Asset::class, ['id' => 'assetId']);
    }

}
