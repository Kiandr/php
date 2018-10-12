<?php
namespace REW\Model\Idx\Search\Panel;

use REW\Model\Idx\Search\PanelRequestInterface;

class PanelRequest implements PanelRequestInterface
{
    /**
     * @var string
     */
    protected $feed;

    /**
     * @param string $feed
     * @return self
     */
    public function withFeed($feed)
    {
        $clone = clone $this;
        $clone->feed = $feed;
        return $clone;
    }

    /**
     * @return string
     */
    public function getFeed()
    {
        return $this->feed;
    }
}
