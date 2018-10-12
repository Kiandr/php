<?php
namespace REW\Model\Idx\Map\Radius;

use REW\Model\Idx\Map\Coordinates;

class Radius extends Coordinates
{
    /** @var string */
    const FLD_RADIUS = 'radius';

    /**
     * @var float
     */
    protected $radius;

    /**
     * @param float $radius
     * @return self
     */
    public function withRadius($radius)
    {
        $clone = clone $this;
        $clone->radius = floatval($radius);
        return $clone;
    }

    /**
     * @return float
     */
    public function getRadius()
    {
        return $this->radius;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("%f,%f,%f", $this->latitude, $this->longitude, $this->radius);
    }
}
