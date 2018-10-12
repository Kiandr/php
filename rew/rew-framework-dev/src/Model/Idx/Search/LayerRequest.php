<?php
namespace REW\Model\Idx\Search;

use REW\Model\Idx\Search\Map\Bounds\Bounds;
use REW\Model\Idx\Search\Map\Radius\Radius;

/**
 * Class LayerRequest
 * @package REW\Model\Idx\Search
 */
class LayerRequest
{
    /**
     * @var Bounds
     */
    protected $bounds;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $feed;

    /**
     * @var string
     */
    protected $zip;

    /**
     * @var string
     */
    protected $longitude;

    /**
     * @var string
     */
    protected $latitude;

    /**
     * @var Radius
     */
    protected $radius;

    /**
     * @var string
     */
    protected $polygon;

    /**
     * @var string
     */
    protected $baseSearchUrl;

    /**
     * @param Bounds $bounds
     * @return self
     */
    public function withBounds(Bounds $bounds)
    {
        $clone = clone $this;
        $clone->bounds = $bounds;
        return $clone;
    }

    /**
     * @return Bounds
     */
    public function getBounds()
    {
        return $this->bounds;
    }

    /**
     * @param string $type
     * @return self
     */
    public function withType($type)
    {
        $clone = clone $this;
        $clone->type = $type;
        return $clone;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $latitude
     * @return self
     */
    public function withLatitude($latitude)
    {
        $clone = clone $this;
        $clone->latitude = $latitude;
        return $clone;
    }

    /**
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param string $longitude
     * @return self
     */
    public function withLongitude($longitude)
    {
        $clone = clone $this;
        $clone->longitude = $longitude;
        return $clone;
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param Radius $radius
     * @return self
     */
    public function withRadius(Radius $radius)
    {
        $clone = clone $this;
        $clone->radius = $radius;
        return $clone;
    }

    /**
     * @return Radius
     */
    public function getRadius()
    {
        return $this->radius;
    }

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

    /**
     * @param string $zip
     * @return self
     */
    public function withZip($zip)
    {
        $clone = clone $this;
        $clone->zip = $zip;
        return $clone;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param string $polygon
     * @return self
     */
    public function withPolygon($polygon)
    {
        $clone = clone $this;
        $clone->polygon = $polygon;
        return $clone;
    }

    /**
     * @return string
     */
    public function getPolygon()
    {
        return $this->polygon;
    }

    /**
     * @param string $baseSearchUrl
     * @return self
     */
    public function withBaseSearchUrl($baseSearchUrl)
    {
        $clone = clone $this;
        $clone->baseSearchUrl = $baseSearchUrl;
        return $clone;
    }

    /**
     * @return string
     */
    public function getBaseSearchUrl()
    {
        return $this->baseSearchUrl;
    }
}
