<?php

use REW\Phinx\Migration\AbstractMigration;

class AddLeadAltEmailSettings extends AbstractMigration
{

    /**
     * Add lead alternate email config
     * @return void
     */
    public function up()
    {
        $users = $this->table('users');
        $users->addColumn('email_alt_cc_searches', 'enum', ['values' => ['false','true'], 'default' => 'false', 'after' => 'email_alt'])
            ->update();
    }

    /**
     * Remove lead alternate email config
     * @return void
     */
    public function down()
    {
        $users = $this->table('users');
        $users->removeColumn('email_alt_cc_searches')
            ->update();
    }
}
