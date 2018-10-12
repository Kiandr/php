<?php
namespace REW\Model\Idx\Search;

interface PanelRequestInterface
{
    /**
     * @param string $feed
     * @return self
     */
    public function withFeed($feed);

    /**
     * @return string
     */
    public function getFeed();

}
