<?php
namespace REW\Factory\Idx\Search;

use \REW\Model\Idx\Search\FieldInterface;

interface FieldFactoryInterface
{
    /**
     * Creates a FieldInterface-bound object based on the $data payload.
     * @param array $data
     * @return FieldInterface
     */
    public function createFromArray(array $data);
}
