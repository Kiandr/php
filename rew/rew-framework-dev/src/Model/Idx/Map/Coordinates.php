<?php
namespace REW\Model\Idx\Map;

class Coordinates
{
    /** @var string */
    const FLD_LATITUDE = 'lat';

    /** @var string */
    const FLD_LONGITUDE = 'lng';

    /**
     * @var float
     */
    protected $latitude;

    /**
     * @var float
     */
    protected $longitude;

    /**
     * Coordinate constructor.
     * @param double $latitude
     * @param double $longitude
     */
    public function __construct($latitude = null, $longitude = null)
    {
        $this->latitude = doubleval($latitude);
        $this->longitude = doubleval($longitude);
    }

    /**
     * @param double $latitude
     * @return self
     */
    public function withLatitude($latitude)
    {
        $clone = clone $this;
        $clone->latitude = doubleval($latitude);
        return $clone;
    }

    /**
     * @return double
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param double $longitude
     * @return self
     */
    public function withLongitude($longitude)
    {
        $clone = clone $this;
        $clone->longitude = doubleval($longitude);
        return $clone;
    }

    /**
     * @return double
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("%f,%f", $this->latitude, $this->longitude);
    }
}
