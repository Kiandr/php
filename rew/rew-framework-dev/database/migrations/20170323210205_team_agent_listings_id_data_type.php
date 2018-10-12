<?php

use REW\Phinx\Migration\AbstractMigration;

class TeamAgentListingsIdDataType extends AbstractMigration
{
    /**
     * change datatype of listing id
     */
    public function up()
    {
        $settings = $this->getContainer()->get(\REW\Core\Interfaces\SettingsInterface::class);
        $tableName = $settings['TABLES']['LM_TEAM_AGENT_LISTINGS'];

        $this->table($tableName)
            ->changeColumn('listing_id', 'string', ['length' => 32, 'null' => false, 'default' => ''])
            ->update();
    }

    /**
     * change datatype of listing id
     */
    public function down()
    {
        $settings = $this->getContainer()->get(\REW\Core\Interfaces\SettingsInterface::class);
        $tableName = $settings['TABLES']['LM_TEAM_AGENT_LISTINGS'];

        $this->table($tableName)
            ->changeColumn(
                'listing_id',
                'integer',
                ['length' => \Phinx\Db\Adapter\MysqlAdapter::INT_BIG, 'null' => false, 'signed' => false]
            )
            ->update();
    }
}
