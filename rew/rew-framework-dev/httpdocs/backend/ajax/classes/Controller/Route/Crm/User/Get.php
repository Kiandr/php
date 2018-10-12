<?php

namespace REW\Api\Internal\Controller\Route\Crm\User;

use REW\Api\Internal\Exception\InsufficientPermissionsException;
use REW\Api\Internal\FormatFactory;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Backend\Auth;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\SkinInterface;
use Slim\Http\Response;
use Slim\Http\Request;

/**
 * Authuser Get Controller
 * @package REW\Api\Internal\Controller
 */
class Get implements ControllerInterface
{
    /**
     * This endpoint's supported request parameters
     */
    const PERMITTED_REQUEST_FIELDS = [
        'standard' => [
            'email',
            'first_name',
            'id',
            'image',
            'last_name',
            'type',
            'TZ',
        ],
        'boolean'=> [
            'admin',
            'text_available',
        ],
        'timestamp' => [
        ],
    ];

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var array
     */
    protected $authInfo;

    /**
     * @var array
     */
    protected $classes;

    /**
     * @var array
     */
    protected $get;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var SkinInterface
     */
    protected $skin;

    /**
     * @param AuthInterface $auth
     * @param SettingsInterface $settings
     * @param SkinInterface $skin
     */
    public function __construct(
        AuthInterface $auth,
        SettingsInterface $settings,
        SkinInterface $skin
    ){
        $this->auth = $auth;
        $this->settings = $settings;
        $this->skin = $skin;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $routeParams
     * @throws InsufficientPermissionsException
     */
    public function __invoke(Request $request, Response $response, $routeParams = [])
    {
        $this->get = $request->get();

        $this->authInfo = $this->auth->getInfo();

        if (empty($this->authInfo)) {
            throw new InsufficientPermissionsException('Failed to load CRM user information.');
        }

        $body = $this->getResponse();

        ksort($body);

        $response->setBody(json_encode($body));
    }

    /**
     * @return array
     */
    protected function getPermissions()
    {
        // Namespaces: crm, cms, company, listings, settings
        $return = [];
        $return['crm.can.delete.leads'] = $this->classes['REW\Backend\Auth\LeadsAuth']->canDeleteLeads($this->auth);
        $return['crm.can.manage.groups'] = $this->classes['REW\Backend\Auth\LeadsAuth']->canManageGroups($this->auth);
        $return['crm.can.manage.leads'] = $this->classes['REW\Backend\Auth\LeadsAuth']->canManageLeads($this->auth);
        $return['crm.can.assign.leads'] = $this->classes['REW\Backend\Auth\LeadsAuth']->canAssignLeads($this->auth);
        $return['crm.can.email.all.leads'] = $this->classes['REW\Backend\Auth\LeadsAuth']->canEmailLeads($this->auth);
        $return['crm.can.view.agents'] = $this->classes['REW\Backend\Auth\AgentsAuth']->canViewAgents($this->auth);
        $return['crm.can.view.lenders'] = $this->classes['REW\Backend\Auth\LendersAuth']->canViewLenders($this->auth);
        $return['crm.can.assign.all.action.plans'] = $this->classes['REW\Backend\Auth\LeadsAuth']->canAssignActionPlans($this->auth);
        $return['crm.can.assign.own.action.plans'] = $this->classes['REW\Backend\Auth\LeadsAuth']->canAssignOwnActionPlans($this->auth);
        $return['crm.can.manage.action.plans'] = $this->classes['REW\Backend\Auth\LeadsAuth']->canManageActionPlans($this->auth);
        $return['crm.can.export.leads'] = $this->classes['REW\Backend\Auth\LeadsAuth']->canExportLeads($this->auth);

        ksort($return);

        return $return;
    }

    /**
     * Build the response
     *
     * @return array
     */
    protected function getResponse()
    {
        // Allow user to request specific response fields
        $requestFields = (!empty($this->get['fields']) && is_array($this->get['fields']))
            ? $this->get['fields']
            : [];

        // Build Response
        $response = [];
        $response['auth_type'] = $this->auth->getType();

        // Format Response Values
        $response = array_merge(
            FormatFactory::create('boolean', $this->authInfo, self::PERMITTED_REQUEST_FIELDS['boolean'], $requestFields)->format(),
            FormatFactory::create('standard', $this->authInfo, self::PERMITTED_REQUEST_FIELDS['standard'], $requestFields)->format(),
            FormatFactory::create('timestamp', $this->authInfo, self::PERMITTED_REQUEST_FIELDS['timestamp'], $requestFields)->format()
        );

        if (empty($requestFields) || in_array('text_available', $requestFields)) {
            if ($this->auth->isLender() || $this->auth->isAssociate()) {
                $response['text_available'] = false;
            } else {
                $response['text_available'] = ($this->settings->MODULES['REW_PARTNERS_TWILIO'] ? true : false);
            }
        }

        // Get User Permissions
        if (empty($requestFields) || in_array('permissions', $requestFields)) {
            $this->loadAuthClasses();
            $response['permissions'] = $this->getPermissions();
        }

        return $response;
    }

    /**
     * Load all of the CRM Auth classes
     */
    protected function loadAuthClasses()
    {
        $this->classes = [];
        $this->classes['REW\Backend\Auth\AgentsAuth'] = new Auth\AgentsAuth($this->settings);
        $this->classes['REW\Backend\Auth\LeadsAuth'] = new Auth\LeadsAuth($this->settings);
        $this->classes['REW\Backend\Auth\LendersAuth'] = new Auth\LendersAuth($this->settings);
    }
}
