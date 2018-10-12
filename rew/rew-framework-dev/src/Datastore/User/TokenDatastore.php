<?php
namespace REW\Datastore\User;

use REW\Core\Interfaces\User\SessionInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Factory\User\Token\ResultInterface as UserTokenResultFactory;

/**
 * Class TokenDatastore
 * @package REW\Datastore\Agent
 */
class TokenDatastore
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var FormatInterface
     */
    protected $format;

    /**
     * @var UserTokenResultFactory
     */
    protected $userTokenResultFactory;

    /**
     * @param SessionInterface $session
     * @param FormatInterface $format
     * @param UserTokenResultFactory $userTokenResultFactory
     */
    public function __construct(
        SessionInterface $session,
        FormatInterface $format,
        UserTokenResultFactory $userTokenResultFactory
    ) {
        $this->session = $session;
        $this->format = $format;
        $this->userTokenResultFactory = $userTokenResultFactory;
    }

    /**
     * @return UserResultModel|null
     */
    public function getTokenFromSession()
    {
        $userToken = null;
        if ($this->session->isValid()) {
            $userGuid = $this->session->getUserGuid();
            $userToken = $this->format->toGuid($userGuid);
        }
        return $this->userTokenResultFactory->createFromArray([
            'token' => $userToken,
            'timestamp' => time()
        ]);
    }
}
