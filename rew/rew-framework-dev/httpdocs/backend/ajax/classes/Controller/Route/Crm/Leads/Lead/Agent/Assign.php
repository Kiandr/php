<?php

namespace REW\Api\Internal\Controller\Route\Crm\Leads\Lead\Agent;

use REW\Api\Internal\Exception\InsufficientPermissionsException;
use REW\Api\Internal\Exception\NotFoundException;
use REW\Api\Internal\Exception\ServerSuccessException;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Backend\Auth\Leads\LeadAuth;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use Slim\Http\Response;
use Slim\Http\Request;
use \Backend_Agent;
use \Backend_Lead;

/**
 * Lead Agent Assign Controller
 * @package REW\Api\Internal\Controller
 */
class Assign implements ControllerInterface
{
    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var Backend_Agent
     */
    protected $agent;

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
        $this->agent = $this->fetchAgent();

        $this->checkPermissions();

        $this->assignLead();
    }

    /**
     * Assign the lead to the agent
     * @throws ServerSuccessException
     */
    protected function assignLead()
    {
        // Assign The Lead
        $this->lead->assign($this->agent, $this->auth);

        // Auto-Accept the Lead if the User is Assigning it to Themselves
        if ($this->agent['id'] == $this->auth->info('id')) {
            $this->lead->status('accepted', $this->auth);
        }

        // Notify the Agent
        $this->agent->notifyAgent([$this->lead], $this->auth);

        // If we've made it this far the call was successful
        throw new ServerSuccessException('The lead has been assigned successfully.');
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
    protected function fetchAgent()
    {
        // Check if Agent Exists
        $agent = $this->db->fetch(sprintf(
            "SELECT * FROM `%s` WHERE `id` = :id;",
            $this->settings->TABLES['LM_AGENTS']
        ), ['id' => $this->routeParams['agentId']]);

        if (empty($agent)) {
            throw new NotFoundException('Failed to find an agent with the requested ID.');
        }

        // Load the Agent Object
        return Backend_Agent::load($agent['id']);
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
}
