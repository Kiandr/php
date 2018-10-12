<?php

use REW\Phinx\Migration\AbstractMigration;

class CreateTimelinePagesTable extends AbstractMigration
{
    public function change()
    {
        $settings = $this->getContainer()->get(\REW\Core\Interfaces\SettingsInterface::class);
        $tablePages = $settings['TABLES']['TIMELINE_PAGES'];
        $tablePageVariables = $settings['TABLES']['TIMELINE_PAGE_VARIABLES'];

        $this->table($tablePages, ['id' => false, 'primary_key' => 'guid'])
            ->addColumn('guid', 'binary', ['length' => 16, 'null' => false, 'default' => ''])
            ->addColumn('url', 'string', ['length' => 255, 'null' => false, 'default' => ''])
            ->addColumn('last_page_guid', 'binary', ['length' => 16, 'null' => true, 'default' => null])
            ->addColumn('timestamp_created', 'timestamp', array('default' => 'CURRENT_TIMESTAMP'))
            ->addColumn('timestamp_updated', 'timestamp', array('default' => 'CURRENT_TIMESTAMP'))
            ->addForeignKey(
                'last_page_guid',
                $tablePages,
                'guid',
                ['constraint' => 'fktimeline_page_last', 'delete' => 'SET_NULL', 'update' => 'CASCADE']
            )
            ->create();

        $this->table($tablePageVariables, ['id' => false, 'primary_key' => ['page_guid', 'key']])
            ->addColumn('page_guid', 'binary', ['length' => 16, 'null' => false, 'default' => ''])
            ->addColumn('key', 'string', ['length' => 32, 'null' => false, 'default' => ''])
            ->addColumn(
                'value',
                'text',
                ['length' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_REGULAR, 'null' => false, 'default' => '']
            )
            ->addForeignKey(
                'page_guid',
                $tablePages,
                'guid',
                ['constraint' => 'fktimeline_page_variables', 'delete' => 'CASCADE', 'update' => 'CASCADE']
            )
            ->addColumn('timestamp_created', 'timestamp', array('default' => 'CURRENT_TIMESTAMP'))
            ->addColumn('timestamp_updated', 'timestamp', array('default' => 'CURRENT_TIMESTAMP'))
            ->create();
    }
}
