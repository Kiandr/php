<?php

use REW\Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class CustomFieldsTableUpdate extends AbstractMigration
{
    /**
     * Up Method.
     *
     * Add New Type of Custom Field Table
     * @return void
     */
    public function up()
    {
        // `users_fields`
        $fields = $this->table('users_fields');
        $fields->changeColumn(
            'type',
            'enum',
            ['values' => ['string','number','date','text'], 'default' => 'string', 'comment' => 'Field Type', 'null' => false]
        )->update();

        // `users_field_text`
        $this->table('users_field_text')
            ->addColumn('user_id', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'signed' => false])
            ->addColumn('field_id', 'integer')
            ->addColumn('value', 'text', ['limit' => TEXT_MEDIUM, 'null' => false])
            ->addIndex(['user_id', 'field_id'], ['unique' => true])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('field_id', 'users_fields', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }

    /**
     * Down Method.
     *
     * Remove New Type of Custom Field Table
     * @return void
     */
    public function down()
    {
        // `users_fields`
        $fields = $this->table('users_fields');
        $fields->changeColumn(
            'type',
            'enum',
            ['values' => ['string','number','date'], 'default' => 'string', 'comment' => 'Field Type', 'null' => false]
        )->update();

        // `users_field_text`
        $this->dropTable('users_field_text');
    }
}
