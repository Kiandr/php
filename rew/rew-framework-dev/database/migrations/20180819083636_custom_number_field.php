<?php

use REW\Phinx\Migration\AbstractMigration;

/**
 * More information on writing migrations is available here:
 * http://docs.phinx.org/en/latest/migrations.html
 */
class CustomNumberField extends AbstractMigration
{

    /**
     * Make custom number field an int
     * @return void
     */
    public function up()
    {
        $this->table('users_field_numbers')
            ->changeColumn('value', 'biginteger')
            ->save();
    }

    /**
     * Make custom number field a decimal
     * @return void
     */
    public function down()
    {
        $this->table('users_field_numbers')
            ->changeColumn('value', 'decimal', ['scale' => 10, 'precision' => 0])
            ->save();
    }
}
