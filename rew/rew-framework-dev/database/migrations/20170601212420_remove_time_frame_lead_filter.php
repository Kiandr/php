<?php

use REW\Phinx\Migration\AbstractMigration;

class RemoveTimeFrameLeadFilter extends AbstractMigration
{
    /**
     * Drop `default_timeframe` column
     */
    public function up()
    {
        foreach (['agents', 'associates', 'lenders'] as $table) {
            $this->table($table)->removeColumn('default_timeframe');
        }
    }
}
