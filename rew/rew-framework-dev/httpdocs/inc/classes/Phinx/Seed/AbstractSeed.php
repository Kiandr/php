<?php

namespace REW\Phinx\Seed;

use Phinx\Seed\AbstractSeed as PhinxAbstractSeed;

/**
 * AbstractSeed
 * @package REW\Phinx
 */
class AbstractSeed extends PhinxAbstractSeed
{

    /**
     * Get last insert ID
     * @return string
     */
    protected function lastInsertId()
    {
        $connection = $this->getAdapter()->getConnection();
        return $connection->lastInsertId();
    }
}
