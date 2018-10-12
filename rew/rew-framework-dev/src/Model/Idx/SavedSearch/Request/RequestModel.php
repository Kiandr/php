<?php
namespace REW\Model\Idx\SavedSearch\Request;

use REW\Core\Interfaces\DBInterface;
use REW\Model\Idx\Search\Map\Radius\Radius;
use REW\Model\Idx\Search\FieldInterface;
use REW\Model\Idx\Search\Map\Bounds\Bounds;

class RequestModel implements RequestInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $feed;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var FieldInterface[]
     */
    protected $criteria;

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
     * @var string
     */
    protected $frequency;

    /**
     * @var int
     */
    protected $userId;

    /**
     * @var int
     */
    protected $agentId;

    /**
     * @param int $id
     * @return self
     */
    public function withId($id)
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     * @param string $title
     * @return self
     */
    public function withTitle($title)
    {
        $clone = clone $this;
        $clone->title = $title;
        return $clone;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param FieldInterface[] $criteria
     * @return self
     */
    public function withSearchCriteria($criteria)
    {
        $clone = clone $this;
        $clone->criteria = $criteria;
        return $clone;
    }

    /**
     * @return FieldInterface[]
     */
    public function getSearchCriteria()
    {
        return $this->criteria;
    }

    /**
     * @return string
     */
    public function getSerializedSearchCriteria()
    {
        $criteriaToSerialize = [];
        foreach ($this->criteria as $criterion) {
            $searchValue = $criterion->getSearchValue();
            $searchOp = $criterion->getSearchOperation();

            // Skip empty search values for range fields (including 0).
            if (empty($searchValue) && in_array($searchOp, [
                DBInterface::COMP_LESS_THAN,
                DBInterface::COMP_LESS_THAN_INTERVAL,
                DBInterface::COMP_MORE_THAN,
                DBInterface::COMP_MORE_THAN_INTERVAL])) {
                continue;
            } // Skip empty values for regular search fields (that aren't 0).
                if (empty($searchValue) && !is_numeric($searchValue)) continue;

                $criteriaToSerialize[$criterion->getFormFieldName()] = $criterion->getSearchValue();
        }

        if (!empty($this->bounds)) {
            $neBounds = $this->bounds->getNorthEastBounds();
            $swBounds = $this->bounds->getSouthWestBounds();
        }
        $criteriaToSerialize['map'] = [
            'bounds' => !empty($neBounds),
            'ne' => (!empty($neBounds)) ? (string)$neBounds : '',
            'sw' => (!empty($swBounds)) ? (string)$swBounds : '',
            'radius' => json_decode(json_encode($this->radius), true),
            'polygon' => json_decode($this->polygon, true)
        ];

        return serialize($criteriaToSerialize);
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
     * @param string $frequency
     * @return self
     */
    public function withFrequency($frequency)
    {
        $clone = clone $this;
        $clone->frequency = $frequency;
        return $clone;
    }

    /**
     * @return string
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * @param int $userId
     * @return self
     */
    public function withUserId($userId)
    {
        $clone = clone $this;
        $clone->userId = $userId;
        return $clone;
    }

    /**
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $agentId
     * @return self
     */
    public function withAgentId($agentId)
    {
        $clone = clone $this;
        $clone->agentId = $agentId;
        return $clone;
    }

    /**
     * @return integer
     */
    public function getAgentId()
    {
        return $this->agentId;
    }
}
