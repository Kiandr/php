<?php
namespace REW\Factory\Idx\Search\FeedInfo;

use REW\Factory\Idx\Search\FeedInfoFactoryInterface;
use REW\Model\Idx\FeedInfo\FeedInfo;
use REW\Model\Idx\FeedInfoInterface;

class FeedInfoFactory implements FeedInfoFactoryInterface
{
    /**
     * @param string $name
     * @return FeedInfoInterface
     */
    public function create($name, $title = '')
    {
        $feedInfo = new FeedInfo();
        $feedInfo = $feedInfo->withName($name);
        $feedInfo = $feedInfo->withTitle($title);
        return $feedInfo;
    }
}
