<?php

use REW\Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class DotloopPhaseTwoSchemaUpdates extends AbstractMigration
{
    /**
     * Create new dotloop tables
     * @return void
     */
    public function up()
    {
        // Accounts
        $partners_dotloop_accounts = $this->table('partners_dotloop_accounts');
        $partners_dotloop_accounts->addColumn('dotloop_account_id', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_REGULAR])
            ->addColumn('email', 'string', ['limit' => 100])
            ->addColumn('timestamp_created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('timestamp_updated', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['dotloop_account_id'], ['unique' => true, 'name' => 'index_dotloop_account'])
            ->save();
        $partners_dotloop_accounts->changeColumn('id', 'integer', ['signed' => false, 'identity' => true])
            ->update();

        // Profiles
        $partners_dotloop_profiles = $this->table('partners_dotloop_profiles');
        $partners_dotloop_profiles->addColumn('account_id', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_REGULAR])
            ->addColumn('dotloop_profile_id', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_REGULAR])
            ->addColumn('name', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
            ->addColumn('timestamp_created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('timestamp_updated', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['account_id', 'dotloop_profile_id'], ['unique' => true, 'name' => 'index_dotloop_account_profile'])
            ->save();
        $partners_dotloop_profiles->changeColumn('id', 'integer', ['signed' => false, 'identity' => true])
            ->update();

        // Loops
        $partners_dotloop_loops = $this->table('partners_dotloop_loops');
        $partners_dotloop_loops->addColumn('profile_id', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_REGULAR])
            ->addColumn('dotloop_loop_id', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_REGULAR])
            ->addColumn('name', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
            ->addColumn('status', 'string', ['limit' => 100])
            ->addColumn('transaction_type', 'string', ['limit' => 100])
            ->addColumn('total_task_count', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_SMALL])
            ->addColumn('completed_task_count', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_SMALL])
            ->addColumn('dotloop_created_timestamp', 'string', ['limit' => 100])
            ->addColumn('dotloop_updated_timestamp', 'string', ['limit' => 100])
            ->addColumn('timestamp_created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('timestamp_updated', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['profile_id', 'dotloop_loop_id'], ['unique' => true, 'name' => 'index_dotloop_profile_loop'])
            ->addForeignKey('profile_id', 'partners_dotloop_profiles', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->save();
        $partners_dotloop_loops->changeColumn('id', 'integer', ['signed' => false, 'identity' => true])
            ->update();

        // Participants
        $partners_dotloop_participants = $this->table('partners_dotloop_participants');
        $partners_dotloop_participants->addColumn('loop_id', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_REGULAR])
            ->addColumn('dotloop_participant_id', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_REGULAR])
            ->addColumn('full_name', 'string', ['limit' => 200])
            ->addColumn('email', 'string', ['limit' => 100])
            ->addColumn('role', 'string', ['limit' => 100])
            ->addColumn('timestamp_created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('timestamp_updated', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['loop_id', 'dotloop_participant_id'], ['unique' => true, 'name' => 'index_dotloop_loop_participant'])
            ->addForeignKey('loop_id', 'partners_dotloop_loops', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->save();
        $partners_dotloop_participants->changeColumn('id', 'integer', ['signed' => false, 'identity' => true])
            ->update();

        // Track Leads That Have Been Pushed to Dotloop's System
        $partners_dotloop_connected_users = $this->table('partners_dotloop_connected_users');
        $partners_dotloop_connected_users->addColumn('user_id', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_MEDIUM])
            ->addColumn('dotloop_account_id', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_REGULAR])
            ->addColumn('dotloop_contact_id', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_REGULAR])
            ->addColumn('timestamp_connected', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['user_id', 'dotloop_account_id'], ['unique' => true, 'name' => 'index_dotloop_lead_account'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->save();
        $partners_dotloop_connected_users->changeColumn('id', 'integer', ['signed' => false, 'identity' => true])
            ->update();

        // DotLoop - Local DB Update Tracker
        $partners_dotloop_system = $this->table('partners_dotloop_system');
        $partners_dotloop_system->addColumn('dotloop_account_id', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_REGULAR])
            ->addColumn('dotloop_update_timestamp', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->save();

        // Drop Old DotLoop Schema
        $users = $this->table('users');
        $users->removeColumn('dotloop_user_id')
            ->removeColumn('dotloop_connect_ts')
            ->update();
    }

    /**
     * Drop new dotloop tables
     * @return void
     */
    public function down()
    {
        // Drop New DotLoop Schema
        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
        $this->dropTable('partners_dotloop_accounts');
        $this->dropTable('partners_dotloop_profiles');
        $this->dropTable('partners_dotloop_loops');
        $this->dropTable('partners_dotloop_participants');
        $this->dropTable('partners_dotloop_connected_users');
        $this->dropTable('partners_dotloop_system');
        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");

        // Restore Old DotLoop Schema
        $users = $this->table('users');
        $users->addColumn('dotloop_user_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => null, 'null' => true, 'after' => 'happygrasshopper_data_id'])
            ->addColumn('dotloop_connect_ts', 'timestamp', ['default' => '0000-00-00 00:00:00', 'null' => false, 'after' => 'dotloop_user_id'])
            ->update();
    }
}
