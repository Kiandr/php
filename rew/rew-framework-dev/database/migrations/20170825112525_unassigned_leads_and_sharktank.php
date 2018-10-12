<?php
use REW\Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class UnassignedLeadsAndSharktank extends AbstractMigration
{
    /**
     * Create new Shark Tank user columns
     * @return void
     */
    public function up()
    {
        // Add user Shark Tank columns
        $users = $this->table('users');
        $users->addColumn('in_shark_tank', 'enum', ['values' => ['false','true'], 'default' => 'false', 'after' => 'timestamp_score'])
            ->addColumn('timestamp_in_shark_tank', 'timestamp', ['default' => '0000-00-00 00:00:00'])
            ->addColumn('timestamp_out_shark_tank', 'timestamp', ['default' => '0000-00-00 00:00:00'])
            ->changeColumn('status', 'string', ['limit' => 100, 'default' => 'unassigned'])
            ->update();

        // Add Shark Tank Admin-Toggle Field
        $default_info = $this->table('default_info');
        $default_info->addColumn('shark_tank', 'enum', ['values' => ['false','true'], 'default' => 'false', 'after' => 'auto_optout_actions'])
            ->update();

        // Update "Unassigned" leads to use new status instead of (pending + agent=1)
        $this->execute("UPDATE `users` SET `status` = 'unassigned' WHERE `status` = 'pending' AND `agent` = '1';");
    }

    /**
     * Drop new Shark Tank user columns
     * @return void
     */
    public function down()
    {
        // Remove user Shark Tank columns
        $users = $this->table('users');
        $users->removeColumn('in_shark_tank')
            ->removeColumn('timestamp_in_shark_tank')
            ->removeColumn('timestamp_out_shark_tank')
            ->changeColumn('status', 'string', ['limit' => 100, 'default' => 'pending'])
            ->update();

        // Remove Shark Tank Admin-Toggle Field
        $default_info = $this->table('default_info');
        $default_info->removeColumn('shark_tank')
            ->update();

        // Revert "Unassigned" leads to (pending + agent=1)
        $this->execute("UPDATE `users` SET `status` = 'pending', `agent` = '1' WHERE `status` = 'unassigned';");
    }
}
