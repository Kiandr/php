<?php
namespace REW\Model\Idx\Search\Map\Bounds;

use REW\Model\Idx\Map\Bounds\NorthEastBounds;
use REW\Model\Idx\Map\Bounds\SouthWestBounds;
use REW\Model\Idx\Search\Map\BoundsInterface;

class Bounds implements BoundsInterface
{
    /**
     * @var \REW\Model\Idx\Map\Bounds\NorthEastBounds
     */
    protected $northEastBounds;

    /**
     * @var \REW\Model\Idx\Map\Bounds\SouthWestBounds
     */
    protected $southWestBounds;

    /**
     * @param \REW\Model\Idx\Map\Bounds\NorthEastBounds $northEastBounds
     * @return self
     */
    public function withNorthEastBounds(NorthEastBounds $northEastBounds)
    {
        $clone = clone $this;
        $clone->northEastBounds = $northEastBounds;
        return $clone;
    }

    /**
     * @return \REW\Model\Idx\Map\Bounds\NorthEastBounds
     */
    public function getNorthEastBounds()
    {
        return $this->northEastBounds;
    }

    /**
     * @param \REW\Model\Idx\Map\Bounds\SouthWestBounds $southWestBounds
     * @return self
     */
    public function withSouthWestBounds(SouthWestBounds $southWestBounds)
    {
        $clone = clone $this;
        $clone->southWestBounds = $southWestBounds;
        return $clone;
    }

    /**
     * @return \REW\Model\Idx\Map\Bounds\SouthWestBounds
     */
    public function getSouthWestBounds()
    {
        return $this->southWestBounds;
    }
}
