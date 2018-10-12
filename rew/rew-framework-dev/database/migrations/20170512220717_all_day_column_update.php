<?php

use REW\Phinx\Migration\AbstractMigration;

class AllDayColumnUpdate extends AbstractMigration
{

    /**
     * Update all day calendar events and set new all_day column to true
     * @return void
     */
    public function up()
    {
        $count = $this->execute(
            'UPDATE `calendar_dates`
            SET `all_day` = "true"
            WHERE TIME(`start`) <> "00:00:00"
            OR TIME(`end`) <> "00:00:00";'
        );
    }

    /**
     * Rollback all day calendar event changes, remove the all_day column
     * @return void
     */
    public function down()
    {
        $count = $this->execute(
            'UPDATE `calendar_dates`
            SET `start` = CONCAT(DATE(`start`), " ", "00:00:00"),
            `end` = CONCAT(DATE(`end`), " ", "00:00:00")
            WHERE `all_day` = "true";'
        );
    }
}
