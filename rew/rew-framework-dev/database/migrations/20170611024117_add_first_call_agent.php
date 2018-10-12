<?php

use REW\Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddFirstCallAgent extends AbstractMigration
{
    /**
     * Create partners_firstcallagent and partners_firstcallagent_users
     * @return void
     */
    public function up()
    {
        $partner_fca = $this->table('partners_firstcallagent');
        $partner_fca->addColumn('agent_id', 'integer', array('signed' => false, 'limit' => MysqlAdapter::INT_MEDIUM))
            ->addColumn('api_key', 'string')
            ->addColumn('sending', 'enum', array('values' => array('false','true'), 'default' => 'false'))
            ->addColumn('exclude_agents', 'text')
            ->addForeignKey('agent_id', 'agents', 'id', array('delete' => 'CASCADE', 'update'=> 'NO_ACTION'))
            ->save();

        $partner_fca_users = $this->table('partners_firstcallagent_users');
        $partner_fca_users->addColumn('user_id', 'integer', array('signed' => false, 'limit' => MysqlAdapter::INT_MEDIUM))
            ->addColumn('sent', 'enum', array('values' => array('false','true'), 'default' => 'false'))
            ->addColumn('no_call', 'enum', array('values' => array('false','true'), 'default' => 'false'))
            ->addForeignKey('user_id', 'users', 'id', array('delete' => 'CASCADE', 'update'=> 'NO_ACTION'))
            ->save();
    }

    /**
     * drop partners_firstcallagent and partners_firstcallagent_users
     * @return void
     */
    public function down()
    {
        $this->dropTable('partners_firstcallagent');
        $this->dropTable('partners_firstcallagent_users');
    }
}
