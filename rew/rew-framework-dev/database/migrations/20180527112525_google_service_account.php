<?php

use REW\Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class GoogleServiceAccount extends AbstractMigration
{
    /**
     * Create service account field
     * @return void
     */
    public function change()
    {
        // Add user Shark Tank columns
        $users = $this->table('agents');
        $users->addColumn('network_google_service_account', 'text', ['limit' => MysqlAdapter::TEXT_MEDIUM])
            ->update();
    }
}
