<?php

use REW\Phinx\Migration\AbstractMigration;

class AddTeamImages extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {

        $table = $this->table('teams');
        $table->addColumn('image', 'string', ['limit' => 100, 'default' => '', 'null' => false, 'after' => 'style'])
        ->update();

        $folder = __DIR__ . '/../../httpdocs/uploads/teams';
        if (!is_dir($folder)) {
            mkdir($folder);
        }
    }
}
