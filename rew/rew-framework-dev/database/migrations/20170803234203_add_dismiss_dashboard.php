<?php

use REW\Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

/**
 * More information on writing migrations is available here:
 * http://docs.phinx.org/en/latest/migrations.html
 */
class AddDismissDashboard extends AbstractMigration
{

    /**
     * Migrate Up
     * @return void
     */
    public function up()
    {
        // `dashboard_dismissed`
        if (!$this->hasTable('dashboard_dismissed')) {
            $this->table('dashboard_dismissed')
            ->addColumn('agent', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'signed' => false])
            ->addColumn('event_id', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'signed' => false])
            ->addColumn('event_mode', 'enum', ['values' => ['inquiry', 'message', 'register', 'selling', 'showing'], 'default' => 'register', 'comment' => 'Field Type', 'null' => false])
            ->addColumn('timestamp', 'timestamp', array('default' => 'CURRENT_TIMESTAMP'))
            ->addForeignKey('agent', 'agents', 'id', array('delete' => 'CASCADE', 'update'=> 'CASCADE'))
            ->create();
        }
    }

    /**
     * Migrate Down
     * @return void
     */
    public function down()
    {
        // Drop New DotLoop Schema
        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
        $this->dropTable('dashboard_dismissed');
        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
