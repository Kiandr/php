<?php

use REW\Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

/**
 * More information on writing migrations is available here:
 * http://docs.phinx.org/en/latest/migrations.html
 */
class AddUniqueIndexToDashboardDismissed extends AbstractMigration
{
    /**
     * Migrate Up
     * @return void
     */
    public function up()
    {
        $this->table('dashboard_dismissed')->addIndex(['agent','event_id','event_mode'], [
            'unique' => true,
            'name' => 'agent_event_mode'
        ])->save();
    }

    /**
     * Migrate Down
     * @return void
     */
    public function down()
    {
        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
        $this->table('dashboard_dismissed')->removeIndexByName('agent_event_mode');
        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
