<?php
namespace REW\Model\Idx\Search;

/**
 * Class ListingCountResult
 * @package REW\Model\Idx\Search
 */
class ListingCountResult implements \JsonSerializable
{

    /**
     * @var string
     */
    const FLD_COUNT = 'count';

    /**
     * @var int
     */
    protected $count;

    /**
     * @param int $count
     * @return self
     */
    public function withCount($count)
    {
        $clone = clone $this;
        $clone->count = $count;
        return $clone;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            self::FLD_COUNT=> $this->count
        ];
    }
}
