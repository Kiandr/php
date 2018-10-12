<?php
namespace REW\Model\Idx\Favorite;

/**
 * Class FavoriteListingModel
 * @package REW\Model\Idx\Favorite
 */
class FavoriteListingModel implements \JsonSerializable
{

    /**
     * @var int
     */
    const FLD_ID = 'id';

    /**
     * @var int
     */
    const FLD_USER_ID = 'user_id';

    /**
     * @var int
     */
    const FLD_AGENT_ID = 'agent_id';

    /**
     * @var int
     */
    const FLD_ASSOCIATE = 'associate';

    /**
     * @var string
     */
    const FLD_MLS_NUMBER = 'mls_number';

    /**
     * @var string
     */
    const FLD_TABLE = 'table';

    /**
     * @var string
     */
    const FLD_IDX = 'idx';

    /**
     * @var string
     */
    const FLD_TYPE = 'type';

    /**
     * @var string
     */
    const FLD_CITY = 'city';

    /**
     * @var string
     */
    const FLD_SUBDIVISION = 'subdivision';

    /**
     * @var string
     */
    const FLD_BEDROOMS = 'bedrooms';

    /**
     * @var string
     */
    const FLD_BATHROOMS = 'bathrooms';

    /**
     * @var string
     */
    const FLD_SQFT = 'sqft';

    /**
     * @var string
     */
    const FLD_PRICE = 'price';

    /**
     * @var string
     */
    const FLD_USER_NOTE = 'user_note';

    /**
     * @var int
     */
    const FLD_TIMESTAMP = 'timestamp';

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $userId;

    /**
     * @var int
     */
    protected $agentId;

    /**
     * @var int
     */
    protected $associate;

    /**
     * @var string
     */
    protected $mlsNumber;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $idx;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $subdivision;

    /**
     * @var int
     */
    protected $bedrooms;

    /**
     * @var float
     */
    protected $bathrooms;

    /**
     * @var int
     */
    protected $sqft;

    /**
     * @var int
     */
    protected $price;

    /**
     * @var string
     */
    protected $userNote;

    /**
     * @var int
     */
    protected $timestamp;

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
     * @return int
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
     * @return int
     */
    public function getAgentId()
    {
        return $this->agentId;
    }

    /**
     * @param int $associate
     * @return self
     */
    public function withAssociate($associate)
    {
        $clone = clone $this;
        $clone->associate = $associate;
        return $clone;
    }

    /**
     * @return int
     */
    public function getAssociate()
    {
        return $this->associate;
    }

    /**
     * @param string $mlsNumber
     * @return self
     */
    public function withMlsNumber($mlsNumber)
    {
        $clone = clone $this;
        $clone->mlsNumber = $mlsNumber;
        return $clone;
    }

    /**
     * @return string
     */
    public function getMlsNumber()
    {
        return $this->mlsNumber;
    }

    /**
     * @param string $table
     * @return self
     */
    public function withTable($table)
    {
        $clone = clone $this;
        $clone->table = $table;
        return $clone;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param string $idx
     * @return self
     */
    public function withIdx($idx)
    {
        $clone = clone $this;
        $clone->idx = $idx;
        return $clone;
    }

    /**
     * @return string
     */
    public function getIdx()
    {
        return $this->idx;
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
     * @param string $city
     * @return self
     */
    public function withCity($city)
    {
        $clone = clone $this;
        $clone->city = $city;
        return $clone;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $subdivision
     * @return self
     */
    public function withSubdivision($subdivision)
    {
        $clone = clone $this;
        $clone->subdivision = $subdivision;
        return $clone;
    }

    /**
     * @return string
     */
    public function getSubdivision()
    {
        return $this->subdivision;
    }

    /**
     * @param int $bedrooms
     * @return self
     */
    public function withBedrooms($bedrooms)
    {
        $clone = clone $this;
        $clone->bedrooms = $bedrooms;
        return $clone;
    }

    /**
     * @return int
     */
    public function getBedrooms()
    {
        return $this->bedrooms;
    }

    /**
     * @param float $bathrooms
     * @return self
     */
    public function withBathrooms($bathrooms)
    {
        $clone = clone $this;
        $clone->bathrooms = $bathrooms;
        return $clone;
    }

    /**
     * @return float
     */
    public function getBathrooms()
    {
        return $this->bathrooms;
    }

    /**
     * @param float $sqft
     * @return self
     */
    public function withSqft($sqft)
    {
        $clone = clone $this;
        $clone->sqft = $sqft;
        return $clone;
    }

    /**
     * @return float
     */
    public function getSqft()
    {
        return $this->sqft;
    }

    /**
     * @param int $price
     * @return self
     */
    public function withPrice($price)
    {
        $clone = clone $this;
        $clone->price = $price;
        return $clone;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param string $userNote
     * @return self
     */
    public function withUserNote($userNote)
    {
        $clone = clone $this;
        $clone->userNote = $userNote;
        return $clone;
    }

    /**
     * @return string
     */
    public function getUserNote()
    {
        return $this->userNote;
    }

    /**
     * @param int $timestamp
     * @return self
     */
    public function withTimestamp($timestamp)
    {
        $clone = clone $this;
        $clone->timestamp = $timestamp;
        return $clone;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            self::FLD_ID => $this->id,
            self::FLD_USER_ID => $this->userId,
            self::FLD_AGENT_ID => $this->agentId,
            self::FLD_ASSOCIATE => $this->associate,
            self::FLD_MLS_NUMBER => $this->mlsNumber,
            self::FLD_TABLE => $this->table,
            self::FLD_IDX => $this->idx,
            self::FLD_TYPE => $this->type,
            self::FLD_CITY => $this->city,
            self::FLD_SUBDIVISION => $this->subdivision,
            self::FLD_BEDROOMS => $this->bedrooms,
            self::FLD_BATHROOMS => $this->bathrooms,
            self::FLD_SQFT => $this->sqft,
            self::FLD_PRICE => $this->price,
            self::FLD_USER_NOTE => $this->userNote,
            self::FLD_TIMESTAMP => $this->timestamp
        ];
    }
}
