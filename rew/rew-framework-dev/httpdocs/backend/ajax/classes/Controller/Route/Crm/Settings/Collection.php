<?php

namespace REW\Api\Internal\Controller\Route\Crm\Settings;

use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\CacheInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Api\Internal\Store\Settings;
use REW\Backend\Auth\LeadsAuth;
use \Slim\Http\Response;
use \Slim\Http\Request;


/**
 * Class Collection Controller
 * @package REW\Api\Internal\Controller
 */
class Collection implements ControllerInterface
{
    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var DB
     */
    protected $db;

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @param AuthInterface $auth
     * @param DBInterface $db
     * @param ContainerInterface $container
     * @param SettingsInterface $settings
     */
    public function __construct(
        AuthInterface $auth,
        DBInterface $db,
        ContainerInterface $container,
        SettingsInterface $settings
    ) {
        $this->auth = $auth;
        $this->container = $container;
        $this->db = $db;
        $this->settings = $settings;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $routeParams
     */
    public function __invoke(Request $request, Response $response, $routeParams = [])
    {
        // Get Request
        $this->get = $request->get();

        // Get Settings
        $settingsStore = new Settings($this->db, $this->settings);
        $parnters = $settingsStore->getPartners();
        $leadsAuth = new LeadsAuth($this->settings);

        // Bombbomb integration is setup
        if($leadsAuth->canSendToBombbomb($this->auth)){
            $auth_parnters['bombbomb'] = $parnters['bombbomb'];
        }

        // Send applicable settings
        $body = [
            'partners' => $auth_parnters
        ];

        $response->setBody(json_encode($body));
    }
}