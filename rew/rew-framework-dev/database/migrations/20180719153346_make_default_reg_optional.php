<?php

use REW\Phinx\Migration\AbstractMigration;

/**
 * More information on writing migrations is available here:
 * http://docs.phinx.org/en/latest/migrations.html
 */
class MakeDefaultRegOptional extends AbstractMigration
{

    /**
     * Make default registration setting be optional
     * @return void
     */
    public function up()
    {
        $this->table('rewidx_system')->changeColumn('registration', 'string', array('length' => 10, 'default' => 'optional'));
        $this->execute("UPDATE `rewidx_system` SET `registration` = 'optional' WHERE `idx` = '';");
    }
}
