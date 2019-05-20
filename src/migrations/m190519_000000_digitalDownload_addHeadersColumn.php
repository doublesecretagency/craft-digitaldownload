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

namespace doublesecretagency\digitaldownload\migrations;

use craft\db\Migration;
use yii\base\NotSupportedException;

/**
 * Migration: Add headers column
 * @since 2.1.0
 */
class m190519_000000_digitalDownload_addHeadersColumn extends Migration
{

    /**
     * @inheritdoc
     * @throws NotSupportedException
     */
    public function safeUp()
    {
        $table = '{{%digitaldownload_tokens}}';
        if (!$this->db->columnExists($table, 'headers')) {
            $this->addColumn($table, 'headers', $this->text()->after('token'));
        }
        $this->update($table, ['headers' => '{}']);
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m190519_000000_digitalDownload_addHeadersColumn cannot be reverted.\n";

        return false;
    }

}
