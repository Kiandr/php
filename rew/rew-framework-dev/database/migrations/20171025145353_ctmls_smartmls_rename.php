<?php

use REW\Phinx\Migration\AbstractMigration;

class CtmlsSmartmlsRename extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        // Feeds to be changed
        $oldFeed = 'ctmls';
        $newFeed = 'smartmls';

        $tables = array(
            'rewidx_system',
            'rewidx_defaults',
            'rewidx_quicksearch',
            'users_listings',
            'users_listings_dismissed',
            'users_searches',
            'users_viewed_listings',
            'users_viewed_searches'
        );

        $no_duplicates = [
            'rewidx_system',
            'rewidx_defaults',
            'rewidx_quicksearch',
        ];

        $not_exists = ' AND NOT EXISTS (SELECT `idx` FROM (SELECT `idx` FROM `%s`) as `singleton` WHERE `idx` = \'' . $newFeed . '\')';

        foreach ($tables as $tableName) {

            // Update old feed names in each table
            $this->query(
                "UPDATE `" . $tableName . "` 
                SET `idx` = '" . $newFeed . "' 
                WHERE `idx` = '" . $oldFeed . "'" . (in_array($tableName, $no_duplicates) ? sprintf($not_exists, $tableName) : "") . ";"
            );

        }
    }
}
