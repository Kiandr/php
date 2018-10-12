<?php

use REW\Phinx\Migration\AbstractMigration;

class DefaultSortOrderIsasLenders extends AbstractMigration
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
        foreach (['associates', 'lenders'] as $table) {
            $this->table($table)
                ->addColumn('default_order', 'string', array(
                    'after'   => 'default_filter',
                    'limit'   => 100,
                    'default' => 'active'
                ))
                ->addColumn('default_sort', 'string', array(
                    'after'   => 'default_order',
                    'limit'   => 100,
                    'default' => 'DESC',
                ))
                ->update();
        }
    }
}
