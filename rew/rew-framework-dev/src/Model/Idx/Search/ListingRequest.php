<?php
namespace REW\Model\Idx\Search;

use \InvalidArgumentException;
use REW\Model\Idx\Search\Map\Bounds\Bounds;
use REW\Model\Idx\Search\Map\Radius\Radius;
use REW\Pagination\Pagination;

/**
 * Class ListingRequest
 * @package REW\Model\Idx\Search
 * @todo find a nice way to relate the request data to the IDX fields
 */
class ListingRequest
{
    /**
     * @var Bounds
     */
    protected $bounds;

    /**
     * @var Radius
     */
    protected $radius;

    /**
     * @var string
     */
    protected $polygon;

    /**
     * @var Pagination
     */
    protected $pagination;

    /**
     * @var string
     */
    protected $feedName;

    /**
     * @var string
     */
    protected $baseSearchUrl;

    /**
     * @var array
     */
    protected $agentIds;

    /**
     * @var \REW\Model\Idx\Search\FieldInterface[]
     */
    protected $searchCriteria = [];

    /**
     * @param array $agentIds
     * @return self
     */
    public function withAgentIds($agentIds)
    {
        if (!is_array($agentIds)) {
            throw new InvalidArgumentException('$agentIds must be an array!');
        }

        $clone = clone $this;
        $clone->agentIds = $agentIds;
        return $clone;
    }

    /**
     * @return array
     */
    public function getAgentIds()
    {
        return $this->agentIds;
    }

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
     * @param \REW\Pagination\Pagination $pagination
     * @return self
     */
    public function withPagination(Pagination $pagination)
    {
        $clone = clone $this;
        $clone->pagination = $pagination;
        return $clone;
    }

    /**
     * @return \REW\Pagination\Pagination
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * @param string $feedName
     * @return self
     */
    public function withFeedName($feedName)
    {
        $clone = clone $this;
        $clone->feedName = $feedName;
        return $clone;
    }

    /**
     * @return string
     */
    public function getFeedName()
    {
        return $this->feedName;
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
     * @param FieldInterface[] $searchCriteria
     * @return self
     */
    public function withSearchCriteria(array $searchCriteria)
    {
        $clone = clone $this;
        $clone->searchCriteria = $searchCriteria;
        return $clone;
    }

    /**
     * @param FieldInterface $criterion
     * @return self
     */
    public function withAdditionalSearchCriterion(FieldInterface $criterion)
    {
        $clone = clone $this;
        $clone->searchCriteria[] = $criterion;
        return $clone;
    }

    /**
     * @return FieldInterface[]
     */
    public function getSearchCriteria()
    {
        return $this->searchCriteria;
    }

    /**
     * @return string
     */
    public function getBaseSearchUrl()
    {
        return $this->baseSearchUrl;
    }
}
