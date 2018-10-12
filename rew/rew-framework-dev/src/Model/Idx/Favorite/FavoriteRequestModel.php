<?php
namespace REW\Model\Idx\Favorite;

use \InvalidArgumentException;

class FavoriteRequestModel
{
    /**
     * @var int
     */
    protected $userId;

    /**
     * @var string
     */
    protected $listingId;

    /**
     * @var string
     */
    protected $listingType;

    /**
     * @var string
     */
    protected $feedName;

    /**
     * @param int $userId
     * @return self
     */
    public function withUserId($userId)
    {
        if (!is_numeric($userId)) {
            throw new InvalidArgumentException('$userId must be a number!');
        }

        $clone = clone $this;
        $clone->userId = $userId;
        return $clone;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $listingId
     * @return self
     */
    public function withListingId($listingId)
    {
        if (!is_string($listingId)) {
            throw new InvalidArgumentException('$listingId must be a string!');
        }

        $clone = clone $this;
        $clone->listingId = $listingId;
        return $clone;
    }

    /**
     * @return string
     */
    public function getListingId()
    {
        return $this->listingId;
    }

    /**
     * @param string $listingType
     * @return self
     */
    public function withListingType($listingType)
    {
        if (!is_string($listingType)) {
            throw new InvalidArgumentException('$listingType must be a string!');
        }

        $clone = clone $this;
        $clone->listingType = $listingType;
        return $clone;
    }

    /**
     * @return int
     */
    public function getListingType()
    {
        return $this->listingType;
    }

    /**
     * @param string $feedName
     * @return self
     */
    public function withFeedName($feedName)
    {
        if (!is_string($feedName)) {
            throw new InvalidArgumentException('$feedName must be a string!');
        }

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
}
