<?php
namespace REW\Model\Idx\SavedSearch\Result;

interface ResultInterface extends \JsonSerializable
{
    const FLD_TITLE = 'title';
    const FLD_CRITERIA = 'criteria';
    const FLD_IDX = 'idx';
    const FLD_SUGGESTED = 'suggested';
    const FLD_FREQUENCY = 'frequency';

   /**
    * @param int $id
    * @return self
    */
    public function withId($id);

    /**
     * @return int
     */
    public function getId();

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
     * @param string $criteria
     * @return self
     */
    public function withCriteria($criteria);

    /**
     * @return string
     */
    public function getCriteria();

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
