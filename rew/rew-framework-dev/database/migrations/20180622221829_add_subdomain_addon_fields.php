<?php

use REW\Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

/**
 * More information on writing migrations is available here:
 * http://docs.phinx.org/en/latest/migrations.html
 */
class AddSubdomainAddonFields extends AbstractMigration
{

    /**
     * Migrate Up
     * @return void
     */
    public function up()
    {
        // Add subdomain addon columns - agents
        $agents = $this->table('agents');
        $agents->addColumn('cms_addons', 'text', ['null' => false, 'after' => 'cms_link', 'limit' => MysqlAdapter::TEXT_MEDIUM])
            ->update();

        // Add subdomain addon columns - teams
        $teams = $this->table('teams');
        $teams->addColumn('subdomain_addons', 'text', ['null' => false, 'after' => 'subdomain_idxs', 'limit' => MysqlAdapter::TEXT_MEDIUM])
            ->update();
    }

    /**
     * Migrate Down
     * @return void
     */
    public function down()
    {
        // Remove subdomain addon columns - agents
        $agents = $this->table('agents');
        $agents->removeColumn('cms_addons')
            ->save();

        // Remove subdomain addon columns - teams
        $teams = $this->table('teams');
        $teams->removeColumn('subdomain_addons')
            ->save();
    }

}