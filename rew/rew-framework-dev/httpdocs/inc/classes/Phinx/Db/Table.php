<?php

namespace REW\Phinx\Db;

use Phinx\Db\Table as PhinxTable;

class Table extends PhinxTable
{

    /**
     * Add timestamp columns
     * @return $this
     */
    public function addTimestamps()
    {
        $this->addColumn('timestamp_created', 'timestamp', ['null' => true,  'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('timestamp_updated', 'timestamp', ['null' => true,  'default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
        ;
        return $this;
    }
}
