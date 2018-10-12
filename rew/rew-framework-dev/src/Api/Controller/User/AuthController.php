<?php
namespace REW\Api\Controller\User;

use REW\Datastore\User\TokenDatastore;

/**
 * Class AuthController
 * @package REW\Controller\User
 */
class AuthController
{
    /**
     * @var TokenDatastore
     */
    protected $tokenDatastore;

    /**
     * @param TokenDatastore $tokenDatastore
     */
    public function __construct(TokenDatastore $tokenDatastore)
    {
        $this->tokenDatastore = $tokenDatastore;
    }

    /**
     * @return UserResult
     */
    public function getToken()
    {
       return $this->tokenDatastore->getTokenFromSession();
    }
}
