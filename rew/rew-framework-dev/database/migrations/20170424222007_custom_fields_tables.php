<?php

use REW\Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class CustomFieldsTables extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Add Custom Field Tables
     * @return void
     */
    public function change()
    {
        // `users_fields`
        if (!$this->hasTable('users_fields')) {
            $this->table('users_fields')
                ->addColumn('name', 'string', ['comment' => 'Field Name', 'limit' => 100, 'null' => false])
                ->addColumn('title', 'string', ['comment' => 'Field Title', 'limit' => 255, 'null' => false])
                ->addColumn('type', 'enum', ['values' => ['string','number','date'], 'default' => 'string', 'comment' => 'Field Type', 'null' => false])
                ->addColumn('enabled', 'boolean', ['default' => '0', 'comment' => 'Field Enabled', 'null' => false])
                ->create();
        }

        // `users_field_strings`
        if (!$this->hasTable('users_field_strings')) {
            $this->table('users_field_strings')
                ->addColumn('user_id', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'signed' => false])
                ->addColumn('field_id', 'integer')
                ->addColumn('value', 'string', ['limit' => 255, 'null' => false])
                ->addIndex(['user_id', 'field_id'], ['unique' => true])
                ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('field_id', 'users_fields', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->create();
        }

        // `users_field_numbers`
        if (!$this->hasTable('users_field_numbers')) {
            $this->table('users_field_numbers')
                ->addColumn('user_id', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'signed' => false])
                ->addColumn('field_id', 'integer')
                ->addColumn('value', 'decimal', ['null' => false])
                ->addIndex(['user_id', 'field_id'], ['unique' => true])
                ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('field_id', 'users_fields', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->create();
        }

        // `users_field_dates`
        if (!$this->hasTable('users_field_dates')) {
            $this->table('users_field_dates')
                ->addColumn('user_id', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'signed' => false])
                ->addColumn('field_id', 'integer')
                ->addColumn('value', 'date', ['null' => false])
                ->addIndex(['user_id', 'field_id'], ['unique' => true])
                ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('field_id', 'users_fields', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->create();
        }
    }
}
