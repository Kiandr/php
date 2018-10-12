<?php
namespace REW\Model\User\Token;

/**
 * Class ResultModel
 * @package REW\Model\User\Token
 */
class ResultModel implements \JsonSerializable
{
    const FLD_TOKEN = 'token';

    const FLD_TIMESTAMP = 'timestamp';

    /**
     * @var string
     */
    protected $token;

    /**
     * @var int
     */
    protected $timestamp;

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param string $token
     * @return array
     */
    public function withToken($token)
    {
        $clone = clone $this;
        $clone->token = $token;
        return $clone;
    }

    /**
     * @param int $timestamp
     * @return array
     */
    public function withTimestamp($timestamp)
    {
        $clone = clone $this;
        $clone->timestamp = $timestamp;
        return $clone;
    }

    /**
     * Serialize Timestamp
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            self::FLD_TOKEN => $this->token,
            self::FLD_TIMESTAMP => $this->timestamp
        ];
    }
}
