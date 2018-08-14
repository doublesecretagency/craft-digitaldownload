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

/**
 * Installation Migration
 * @since 2.0.0
 */
class Install extends Migration
{

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTables();
        $this->createIndexes();
        $this->addForeignKeys();
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTableIfExists('{{%digitaldownload_tokens}}');
        $this->dropTableIfExists('{{%digitaldownload_log}}');
    }

    /**
     * Creates the tables.
     *
     * @return void
     */
    protected function createTables()
    {
        $this->createTable('{{%digitaldownload_tokens}}', [
            'id'             => $this->primaryKey(),
            'assetId'        => $this->integer()->notNull(),
            'token'          => $this->string(),
            'enabled'        => $this->boolean()->defaultValue(true),
            'expires'        => $this->dateTime(),
            'requireUser'    => $this->string(),
            'maxDownloads'   => $this->integer()->defaultValue(0),
            'totalDownloads' => $this->integer()->defaultValue(0),
            'lastDownloaded' => $this->dateTime(),
            'dateCreated'    => $this->dateTime()->notNull(),
            'dateUpdated'    => $this->dateTime()->notNull(),
            'uid'            => $this->uid(),
        ]);
        $this->createTable('{{%digitaldownload_log}}', [
            'id'          => $this->primaryKey(),
            'tokenId'     => $this->integer(),
            'assetId'     => $this->integer(),
            'userId'      => $this->integer(),
            'ipAddress'   => $this->string(),
            'success'     => $this->boolean()->defaultValue(false),
            'error'       => $this->string(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid'         => $this->uid(),
        ]);
    }

    /**
     * Creates the indexes.
     *
     * @return void
     */
    protected function createIndexes()
    {
        $this->createIndex(null, '{{%digitaldownload_tokens}}', ['token'], true);
    }

    /**
     * Adds the foreign keys.
     *
     * @return void
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey(null, '{{%digitaldownload_tokens}}', ['assetId'], '{{%assets}}', ['id'], 'CASCADE');
        $this->addForeignKey(null, '{{%digitaldownload_log}}', ['assetId'], '{{%assets}}', ['id'], 'SET NULL');
        $this->addForeignKey(null, '{{%digitaldownload_log}}', ['tokenId'], '{{%digitaldownload_tokens}}', ['id'], 'SET NULL');
        $this->addForeignKey(null, '{{%digitaldownload_log}}', ['userId'], '{{%users}}', ['id'], 'SET NULL');
    }

}
