<?php
namespace REW\Factory\Idx\Search;

use REW\Model\Idx\FeedInfoInterface;

interface FeedInfoFactoryInterface
{
    /**
     * @param string $name
     * @return FeedInfoInterface
     */
    public function create($name);
}
