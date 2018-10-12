<?php
namespace REW\Model\Idx\SavedSearch\Request;

use REW\Model\Idx\Search\FieldInterface;

interface RequestInterface
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

    /**
     * @param string $title
     * @return self
     */
    public function withTitle($title);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param FieldInterface[] $criteria
     * @return self
     */
    public function withSearchCriteria($criteria);

    /**
     * @return FieldInterface[]
     */
    public function getSearchCriteria();

    /**
     * @return string
     */
    public function getSerializedSearchCriteria();

    /**
     * @param string $frequency
     * @return self
     */
    public function withFrequency($frequency);

    /**
     * @return string
     */
    public function getFrequency();

    /**
     * @param int $userId
     * @return self
     */
    public function withUserId($userId);

    /**
     * @return integer
     */
    public function getUserId();

    /**
     * @param int $agentId
     * @return self
     */
    public function withAgentId($agentId);

    /**
     * @return integer
     */
    public function getAgentId();
}
