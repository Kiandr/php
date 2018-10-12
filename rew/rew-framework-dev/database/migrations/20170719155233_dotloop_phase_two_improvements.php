<?php

use REW\Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class DotloopPhaseTwoImprovements extends AbstractMigration
{
    /**
     * Create new dotloop table index
     * @return void
     */
    public function up()
    {
        // DotLoop - Local DB Update Tracker
        $partners_dotloop_system = $this->table('partners_dotloop_system');
        $partners_dotloop_system->addIndex(['dotloop_account_id'], ['unique' => true, 'name' => 'index_dotloop_update_account'])
            ->changeColumn('id', 'integer', ['signed' => false, 'identity' => true])
            ->update();

        // Track participants who have been removed from loops
        $partners_dotloop_participants = $this->table('partners_dotloop_participants');
        $partners_dotloop_participants->addColumn('removed_from_loop', 'enum', ['values' => ['false','true'], 'default' => 'false', 'after' => 'role'])
            ->update();

        // Table to track failed "immediate data sync" attempts. Used to identify + re-attempt in auto-sync script
        $partners_dotloop_delayed_syncs = $this->table('partners_dotloop_delayed_syncs');
        $partners_dotloop_delayed_syncs->addColumn('dotloop_account_id', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_REGULAR])
            ->addColumn('dotloop_profile_id', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_REGULAR])
            ->addColumn('dotloop_loop_id', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_REGULAR])
            ->addColumn('timestamp_created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['dotloop_account_id', 'dotloop_profile_id', 'dotloop_loop_id'], ['unique' => true, 'name' => 'index_dotloop_delay_loop_sync'])
            ->save();
        $partners_dotloop_delayed_syncs->changeColumn('id', 'integer', ['signed' => false, 'identity' => true])
            ->update();
    }

    /**
     * Drop new dotloop table index
     * @return void
     */
    public function down()
    {
        // DotLoop - Local DB Update Tracker
        $partners_dotloop_system = $this->table('partners_dotloop_system');
        $partners_dotloop_system->removeIndexByName('index_dotloop_update_account')
            ->changeColumn('id', 'integer', ['signed' => true, 'identity' => true])
            ->update();

        // Remove new "removed from loops" field
        $partners_dotloop_participants = $this->table('partners_dotloop_participants');
        $partners_dotloop_participants->removeColumn('removed_from_loop')
            ->update();

        // Remove table to track failed "immediate data sync" attempts
        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
        $this->dropTable('partners_dotloop_delayed_syncs');
        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
