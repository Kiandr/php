<?php
namespace REW\Datastore\User;

use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Model\User\Search\UserRequestModel;
use REW\Model\User\Search\UserResultModel;
use REW\Factory\User\UserFactory;

/**
 * Class SearchDatastore
 * @package REW\Datastore\Agent
 */
class SearchDatastore
{
    /**
     * Default Fields to Fetch
     * @var array
     */
    const FIELDS = [
        'id',
        'agent',
        'first_name',
        'last_name',
        'email',
        'phone',
        'notify_favs',
        'notify_searches',
        'guid'
    ];

    /**
     * @var DBFactoryInterface
     */
    protected $dbFactory;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var UserFactory
     */
    protected $userFactory;

    /**
     * @param DBFactoryInterface $dbFactory
     * @param SettingsInterface $settings
     * @param UserFactory $userFactory
     */
    public function __construct(DBFactoryInterface $dbFactory, SettingsInterface $settings, UserFactory $userFactory)
    {
        $this->dbFactory = $dbFactory;
        $this->settings = $settings;
        $this->userFactory = $userFactory;
    }

    /**
     * @param UserRequestModel $userRequest
     * @return UserResultModel
     * @throws \PDOException on database error
     */
    public function getUserFromId(UserRequestModel $userRequest)
    {
        if (!$id = $userRequest->getId()) {
            throw new \InvalidArgumentException('An id is requried to getUserFromToken');
        }
        $db = $this->dbFactory->get();

        // Get the User ID from the guid
        $query = $db->prepare(sprintf(
            "SELECT %s FROM `%s` `u` WHERE `u`.`id` = ? LIMIT 1;",
            $this->getFieldSql(),
            $this->settings['TABLES']['LM_LEADS']
        ));
        $query->execute([$id]);
        $userData = $query->fetch();
        if ($userData) {
            return $this->userFactory->createFromArray($userData);
        }
        return null;
    }

    /**
     * @param UserRequestModel $userRequest
     * @return UserResultModel
     * @throws \PDOException on database error
     */
    public function getUserFromToken(UserRequestModel $userRequest)
    {
        if (!$token = $userRequest->getToken()) {
            throw new \InvalidArgumentException('A token is requried to getUserFromToken');
        }
        $db = $this->dbFactory->get();

        // Get the User ID from the guid
        $query = $db->prepare(sprintf(
            "SELECT %s FROM `%s` `u` WHERE `u`.`guid` = GuidToBinary(?) LIMIT 1;",
            $this->getFieldSql(),
            $this->settings['TABLES']['LM_LEADS']
        ));
        $query->execute([$token]);
        $userData = $query->fetch();
        if ($userData) {
            return $this->userFactory->createFromArray($userData);
        }
        return null;
    }

    /**
     * Return a list of fields to query
     * @return array
     */
    protected function getFieldSql()
    {
        return implode(', ', array_map(function($field) {
            return sprintf('`u`.`%s`', $field);
        }, self::FIELDS));
    }
}
