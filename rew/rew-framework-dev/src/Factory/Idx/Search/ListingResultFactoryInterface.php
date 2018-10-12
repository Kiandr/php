<?php
namespace REW\Factory\Idx\Search;

use \REW\Model\Idx\Search\ListingResultInterface;

interface ListingResultFactoryInterface
{
    /**
     * @param array $data
     * @return ListingResultInterface
     */
    public function createFromArray(array $data);
}
