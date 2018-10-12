<?php

namespace REW\Api\Internal\Controller\Route\Crm\Leads\Lead\Agent;

use REW\Api\Internal\Exception\InsufficientPermissionsException;
use REW\Api\Internal\Exception\NotFoundException;
use REW\Api\Internal\Exception\ServerSuccessException;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use Slim\Http\Response;
use Slim\Http\Request;
use REW\Backend\Auth\Leads\LeadAuth;
use \Backend_Lead;

/**
 * Lead Agent Unassign Controller
 * @package REW\Api\Internal\Controller
 */
class Unassign implements ControllerInterface
{
    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var DB
     */
    protected $db;

    /**
     * @var Backend_Lead
     */
    protected $lead;

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @param AuthInterface $auth
     * @param DBInterface $db
     * @param SettingsInterface $settings
     */
    public function __construct(
        AuthInterface $auth,
        DBInterface $db,
        SettingsInterface $settings
    ) {
        $this->auth = $auth;
        $this->db = $db;
        $this->settings = $settings;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $routeParams
     * @return array
     */
    public function __invoke(Request $request, Response $response, $routeParams = [])
    {
        $this->routeParams = $routeParams;

        $this->lead = $this->fetchLead();

        $this->checkPermissions();

        $this->unassignLead();
    }

    /**
     * Check the user's permissions for this request
     * @throws InsufficientPermissionsException
     */
    protected function checkPermissions()
    {
        // Check Request VS Permissions
        $leadAuth = new LeadAuth($this->settings, $this->auth, $this->lead);

        if (!$leadAuth->canManageLead()) {
            throw new InsufficientPermissionsException('You do not have the proper CRM permission to manage this lead.');
        }

        if (!$leadAuth->canAssignAgentToLead()) {
            throw new InsufficientPermissionsException('You do not have the proper CRM permission to assign this lead to an agent.');
        }
    }

    /**
     * @return array
     * @throws NotFoundException
     */
    protected function fetchLead()
    {
        // Check if Lead Exists
        $lead = $this->db->fetch(sprintf(
            "SELECT * FROM `%s` WHERE `id` = :id;",
            $this->settings->TABLES['LM_LEADS']
        ), ['id' => $this->routeParams['leadId']]);

        if (empty($lead)) {
            throw new NotFoundException('Failed to find a lead with the requested ID.');
        }

        // Load the Lead Object
        return new Backend_Lead($lead);
    }

    /**
     * Unassign the lead
     * @throws ServerSuccessException
     */
    protected function unassignLead()
    {
        // Unassign The Lead
        // Changing to 'unassigned' status triggers assignment to the super admin
        $this->lead->status('unassigned');

        // If we've made it this far the call was successful
        throw new ServerSuccessException('The lead has been unassigned successfully.');
    }
}
