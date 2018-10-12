<?php
namespace REW\Model\User\Search;

/**
 * Class UserResult
 * @package REW\Model\User\Search
 */
class UserResultModel
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $agent;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var string
     */
    protected $guid;

    /**
     * @var bool
     */
    protected $notifyFavs;

    /**
     * @var bool
     */
    protected $notifySearches;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * @return bool
     */
    public function getNotifyFavs()
    {
        return $this->notifyFavs;
    }

    /**
     * @return bool
     */
    public function getNotifySearches()
    {
        return $this->notifySearches;
    }

    /**
     * @param int $id
     * @return UserModel
     */
    public function withId($id)
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    /**
     * @param int $id
     * @return UserModel
     */
    public function withAgent($agent)
    {
        $clone = clone $this;
        $clone->agent = $agent;
        return $clone;
    }

    /**
     * @param string $firstName
     * @return UserModel
     */
    public function withFirstName($firstName)
    {
        $clone = clone $this;
        $clone->firstName = $firstName;
        return $clone;
    }

    /**
     * @param string $lastName
     * @return UserModel
     */
    public function withLastName($lastName)
    {
        $clone = clone $this;
        $clone->lastName = $lastName;
        return $clone;
    }

    /**
     * @param string $email
     * @return UserModel
     */
    public function withEmail($email)
    {
        $clone = clone $this;
        $clone->email = $email;
        return $clone;
    }

    /**
     * @param string $phone
     * @return UserModel
     */
    public function withPhone($phone)
    {
        $clone = clone $this;
        $clone->phone = $phone;
        return $clone;
    }

    /**
     * @param string $guid
     * @return UserModel
     */
    public function withGuid($guid)
    {
        $clone = clone $this;
        $clone->guid = $guid;
        return $clone;
    }

    /**
     * @param bool $notifyFavs
     * @return UserModel
     */
    public function withNotifyFavs($notifyFavs)
    {
        $clone = clone $this;
        $clone->notifyFavs = $notifyFavs;
        return $clone;
    }

    /**
     * @param bool $notifySearches
     * @return UserModel
     */
    public function withNotifySearches($notifySearches)
    {
        $clone = clone $this;
        $clone->notifySearches = $notifySearches;
        return $clone;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'agent' => $this->agent,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'guid' => $this->guid,
            'notifyFavs' => $this->notifyFavs,
            'notifySearch' => $this->notifySearch
        ];
    }
}
