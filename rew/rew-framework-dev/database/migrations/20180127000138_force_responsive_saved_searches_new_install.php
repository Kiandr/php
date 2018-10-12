<?php

use REW\Phinx\Migration\AbstractMigration;

/**
 * More information on writing migrations is available here:
 * http://docs.phinx.org/en/latest/migrations.html
 */
class ForceResponsiveSavedSearchesNewInstall extends AbstractMigration
{

    /**
     * Migrate Up
     * @return void
     */
    public function up()
    {
        // Add force_savedsearches_responsive to rewidx_system table
        $rewidx_system = $this->table('rewidx_system');
        $rewidx_system->addColumn('force_savedsearches_responsive', 'enum', ['values' => ['false','true'], 'default' => 'false'])
            ->update();
    }

    /**
     * Migrate Down
     * @return void
     */
    public function down()
    {
        // Remove force_savedsearches_responsive from rewidx_system
        $rewidx_system = $this->table('rewidx_system');
        $rewidx_system->removeColumn('force_savedsearches_responsive')
            ->update();
    }

}