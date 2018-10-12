<?php

use REW\Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

/**
 * More information on writing migrations is available here:
 * http://docs.phinx.org/en/latest/migrations.html
 */
class AddUserRegistrationSite extends AbstractMigration
{

    /**
     * Create new user site columns
     * @return void
     */
    public function up()
    {
        $users = $this->table('users');
        $users->addColumn('site', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'signed' => false, 'default' => 1, 'after' => 'lender'])
            ->addColumn('site_type', 'enum', ['values' => ['agent','team','domain'], 'default' => 'domain', 'null' => false, 'after' => 'site'])
            ->update();
    }

    /**
     * Drop new user site column
     * @return void
     */
    public function down()
    {
        $users = $this->table('users');
        if ($users->hasColumn('site')) {
            $users->removeColumn('site')
                ->update();
        }
        if ($users->hasColumn('site_type')) {
            $users->removeColumn('site_type')
                ->update();
        }
    }

}