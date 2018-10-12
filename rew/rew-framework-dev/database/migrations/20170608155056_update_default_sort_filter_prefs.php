<?php

use REW\Phinx\Migration\AbstractMigration;

class UpdateDefaultSortFilterPrefs extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('agents')
            ->changeColumn('default_order', 'string', array('default' => 'active'))
            ->changeColumn('default_filter', 'string', array('default' => 'pending'))
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('agents')
            ->changeColumn('default_order', 'string', array('default' => 'score'))
            ->changeColumn('default_filter', 'string', array('default' => 'all'))
            ->update();
    }
}
