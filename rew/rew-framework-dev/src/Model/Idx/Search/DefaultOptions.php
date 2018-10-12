<?php
namespace REW\Model\Idx\Search;

class DefaultOptions implements DefaultOptionsInterface
{

    /**
     * @var array
     */
    protected $criteria;

    /**
     * @var string
     */
    protected $limit;

    /**
     * @var string
     */
    protected $sort;

    /**
     * @var string
     */
    protected $map;

    /**
     * @param array $criteria
     * @return self
     */
    public function withCriteria(array $criteria)
    {
        $clone = clone $this;
        $clone->criteria = $criteria;
        return $clone;
    }

    /**
     * @return array
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param string $limit
     * @return self
     */
    public function withLimit($limit)
    {
        $clone = clone $this;
        $clone->limit = $limit;
        return $clone;
    }

    /**
     * @return string
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param string $sort
     * @return self
     */
    public function withSort($sort)
    {
        $clone = clone $this;
        $clone->sort = $sort;
        return $clone;
    }

    /**
     * @return string
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param string $mapDisplay
     * @return self
     */
    public function withMapDisplay($mapDisplay)
    {
        $clone = clone $this;
        $clone->map = $mapDisplay;
        return $clone;
    }

    /**
     * @return string
     */
    public function getMapDisplay()
    {
        return $this->map;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'default_filters' => $this->criteria,
            'default_page_limit' => $this->limit,
            'default_sort_by' => $this->sort,
            'default_map' => $this->map
        ];
    }
}
