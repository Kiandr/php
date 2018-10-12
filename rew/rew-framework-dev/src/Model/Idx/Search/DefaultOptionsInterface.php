<?php
namespace REW\Model\Idx\Search;

interface DefaultOptionsInterface
{
    /**
     * Set the default search criteria
     * @param array $criteria
     * @return self
     */
    public function withCriteria(array $criteria);

    /**
     * Get the default search criteria
     * @return array
     */
    public function getCriteria();

    /**
     * @param string $limit
     * @return self
     */
    public function withLimit($limit);

    /**
     * @return string
     */
    public function getLimit();

    /**
     * @param string $sort
     * @return self
     */
    public function withSort($sort);

    /**
     * @return string
     */
    public function getSort();

    /**
     * @param string $sort
     * @return self
     */
    public function withMapDisplay($mapDisplay);

    /**
     * @return string
     */
    public function getMapDisplay();

    /**
     * @return array
     */
    public function toArray();
}
