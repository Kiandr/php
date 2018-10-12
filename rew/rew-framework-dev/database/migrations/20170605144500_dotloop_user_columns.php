<?php

use REW\Phinx\Migration\AbstractMigration;

class DotloopUserColumns extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('users');
        $table->addColumn('dotloop_user_id', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_REGULAR, 'default' => null, 'null' => true, 'after' => 'happygrasshopper_data_id'])
            ->update();
        $table->addColumn('dotloop_connect_ts', 'timestamp', ['default' => '0000-00-00 00:00:00', 'null' => false, 'after' => 'dotloop_user_id'])
            ->update();
    }

    public function down()
    {
        $table = $this->table('users');
        $table->removeColumn('dotloop_user_id')
            ->update();
        $table->removeColumn('dotloop_connect_ts')
            ->update();
    }
}
