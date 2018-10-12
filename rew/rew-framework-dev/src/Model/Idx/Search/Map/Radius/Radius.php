<?php
namespace REW\Model\Idx\Search\Map\Radius;

use REW\Model\Idx\Search\Map\RadiusInterface;

class Radius implements RadiusInterface
{
    /**
     * @var \REW\Model\Idx\Map\Radius\Radius[]
     */
    protected $radii = [];

    /**
     * @param \REW\Model\Idx\Map\Radius\Radius[] $radii
     * @return self
     */
    public function withRadii($radii)
    {
        $clone = clone $this;
        $clone->radii = $radii;
        return $clone;
    }

    /**
     * @return \REW\Model\Idx\Map\Radius\Radius[]
     */
    public function getRadii()
    {
        return $this->radii;
    }

    /**
     * @return mixed|\REW\Model\Idx\Map\Radius\Radius[]
     */
    public function jsonSerialize()
    {
        return array_map(function($elem) {
            return (string) $elem;
        }, $this->radii);
    }

}
