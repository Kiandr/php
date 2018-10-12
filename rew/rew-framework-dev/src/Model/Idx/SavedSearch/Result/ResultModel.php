<?php
namespace REW\Model\Idx\SavedSearch\Result;

class ResultModel implements ResultInterface
{
    /**
     * @var string
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
     * @var string serialized search criteria
     */
    protected $criteria;

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
     * @return int
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
     * @param string $criteria
     * @return self
     */
    public function withCriteria($criteria)
    {
        $clone = clone $this;
        $clone->criteria = $criteria;
        return $clone;
    }

    /**
     * @return string
     */
    public function getCriteria()
    {
        return $this->criteria;
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

    public function jsonSerialize()
    {
        return [
            self::FLD_CRITERIA => $this->criteria,
            self::FLD_TITLE => $this->title,
            self::FLD_FREQUENCY => $this->frequency,
            self::FLD_IDX => $this->feed
        ];
    }
}
