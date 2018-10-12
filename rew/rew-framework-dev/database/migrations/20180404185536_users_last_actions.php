<?php

use REW\Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

/**
 * More information on writing migrations is available here:
 * http://docs.phinx.org/en/latest/migrations.html
 */
class UsersLastActions extends AbstractMigration
{

    /**
     * Migrate Up
     * Create last_action column on users table and gather last action for all leads
     * @return void
     */
    public function up()
    {
        $users = $this->table('users');
        $users->addColumn('last_action', 'integer', [
            'limit' => MysqlAdapter::INT_MEDIUM,
            'signed' => false,
            'after' => 'num_messages',
            'null' => true
        ])
            ->update();
    }
    /**
     * Migrate Down
     * Drop last_action column
     * @return void
     */
    public function down()
    {
        $users = $this->table('users');
        $users->removeColumn('last_action')->update();
    }

}