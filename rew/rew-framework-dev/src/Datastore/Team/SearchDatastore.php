<?php
namespace REW\Datastore\Team;

use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Core\Interfaces\SettingsInterface;


/**
 * Class SearchDatastore
 * @package REW\Datastore\Agent
 */
class SearchDatastore
{
    /**
     * @var DBFactoryInterface
     */
    protected $dbFactory;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @param DBFactoryInterface $dbFactory
     * @param SettingsInterface $settings
     * @param AgentFactory $agentFactory
     */
    public function __construct(
        DBFactoryInterface $dbFactory,
        SettingsInterface $settings
    ) {
        $this->dbFactory = $dbFactory;
        $this->settings = $settings;
    }

    /**
     * Get CMS Addons for team subdomain
     * @param $agentId
     * @return array
     * @throws \PDOException
     */
    public function getAddons($teamId)
    {
        $addons = [];
        $database = $this->dbFactory->get();

        // Addons query
        $query = 'SELECT `subdomain_addons` FROM `teams` WHERE `id` = :team;';
        $addons = ['team' => $teamId];

        $addonStmt = $database->prepare($query);
        $addonStmt->execute($addons);
        
        $addonResult = $addonStmt->fetch();
        $addons = explode(',', $addonResult['subdomain_addons']);
        return $addons;
    }
}