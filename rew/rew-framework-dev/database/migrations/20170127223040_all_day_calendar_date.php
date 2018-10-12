<?php

use REW\Phinx\Migration\AbstractMigration;

class AllDayCalendarDate extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Add all_day column to calendar_dates table.
     * @return void
     */
    public function change()
    {
        $table = $this->table('calendar_dates');
        $table->addColumn('all_day', 'enum', ['values' => ['true', 'false'], 'default' => 'false', 'null' => false, 'after' => 'end'])
            ->update();
    }
}
