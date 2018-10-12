<?php

use REW\Phinx\Migration\AbstractMigration;

class TeamAgentsAutoAssignTime extends AbstractMigration
{
    /**
     * add missing auto_assign_time field
     */
    public function change()
    {
        $settings = $this->getContainer()->get(\REW\Core\Interfaces\SettingsInterface::class);
        $tableName = $settings['TABLES']['LM_TEAM_AGENTS'];

        $this->table($tableName)
            ->addColumn('auto_assign_time', 'timestamp', [
                'null' => false, 'default' => '0000-00-00 00:00:00'
            ])
            ->addIndex('auto_assign_time')
            ->update();
    }
}
