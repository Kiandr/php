<?php
namespace REW\Model\User\Search;

use \InvalidArgumentException;

/**
 * Class UserRequest
 * @package REW\Model\User\Search
 */
class UserRequestModel
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $token;

    /**
     * @param int $id
     * @return UserRequest
     */
    public function withId($id)
    {
        if (!is_int($id)) {
            throw new InvalidArgumentException('$int must be an integer!');
        }
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
     * @param string $token
     * @return UserRequest
     */
    public function withToken($token)
    {
        if (!is_string($token)) {
            throw new InvalidArgumentException('$token must be a string!');
        }
        $clone = clone $this;
        $clone->token = $token;
        return $clone;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

}
