<?php

use REW\Phinx\Migration\AbstractMigration;

class DropFlyersTable extends AbstractMigration
{

    /**
     * Drop `flyers` table
     */
    public function up()
    {
        $this->dropTable('flyers');
    }
}
