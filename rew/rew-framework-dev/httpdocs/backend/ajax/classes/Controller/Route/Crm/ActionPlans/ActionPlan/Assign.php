<?php

namespace REW\Api\Internal\Controller\Route\Crm\ActionPlans\ActionPlan;

use REW\Api\Internal\Exception\BadRequestException;
use REW\Api\Internal\Exception\InsufficientPermissionsException;
use REW\Api\Internal\Exception\NotFoundException;
use REW\Api\Internal\Exception\ServerSuccessException;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Backend\Auth\Leads\LeadAuth;
use REW\Backend\Auth\LeadsAuth;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use Slim\Http\Response;
use Slim\Http\Request;
use \Backend_Agent;
use \Backend_Lead;
use \Backend_ActionPlan;
use \Exception;

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
    protected $plan;

    /**
     * @var DB
     */
    protected $db;

    /**
     * @var array
     */
    protected $leads;

    /**
     * @var array
     */
    protected $post;

    /**
     * @var SettingsInterface
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
        $body = json_decode($request->getBody());
        $this->post = (!empty($body) ? (array) $body : []);
        $this->routeParams = $routeParams;

        $this->leads = $this->fetchLeads();
        $this->plan = $this->fetchPlan();

        $this->checkPermissions();

        $this->assignLeads();
    }

    /**
     * Assign the lead to the agent
     * @throws BadRequestException If all of the leads have already been assigned to the action plan
     * @throws ServerSuccessException
     */
    protected function assignLeads()
    {

        $plan = Backend_ActionPlan::load($this->plan['id']);

        $already_assigned = [];

        foreach ($this->leads as $lead) {
            try {
                $plan->assign($lead['id'], $this->auth);
            } catch (Exception $e) {
                $already_assigned[] = $lead['id'];
            }
        }

        if (count($already_assigned) >= count($this->leads)) {
            throw new BadRequestException('All selected leads have already been assigned to this action plan.');
        }

        // If we've made it this far the call was successful
        throw new ServerSuccessException('The lead has been assigned successfully.');
    }

    /**
     * Check the user's permissions for this request
     * @throws InsufficientPermissionsException
     */
    protected function checkPermissions()
    {
        $leadsAuth = new LeadsAuth($this->settings);
        if (!$leadsAuth->canAssignActionPlans($this->auth)) {
            foreach ($this->leads as $lead) {
                // Check Request VS Permissions
                $leadAuth = new LeadAuth($this->settings, $this->auth, $lead);

                if (!$leadAuth->canAssignActionPlans()) {
                    throw new InsufficientPermissionsException('You do not have the proper CRM permission to assign this lead to an Action Plan.');
                }
            }
        }
    }

    /**
     * @return array
     * @throws NotFoundException
     */
    protected function fetchPlan()
    {
        $plan = $this->db->fetch(sprintf(
            "SELECT "
            . " `day_adjust`, "
            . " `description`, "
            . " `id`, "
            . " `name`, "
            . " `style`, "
            . " `timestamp_created`, "
            . " `timestamp_updated` "
            . " FROM %s "
            . " WHERE `id` = :action_id ",
            $this->settings->TABLES['LM_ACTION_PLANS']
        ), [
            'action_id' => $this->routeParams['actionId']
        ]);

        return $plan;
    }

    /**
     * @return array
     * @throws NotFoundException
     */
    protected function fetchLeads()
    {

        $reqLeads = (!empty($this->post['lead_ids']) && is_array($this->post['lead_ids'])) ? $this->post['lead_ids'] : [];
        $reqLeads = (!empty($this->post['lead_ids']) && !is_array($this->post['lead_ids'])) ? explode(" ", $this->post['lead_ids']) : $reqLeads;

        // Check if Leads Exist
        $leads = $this->db->fetchAll(sprintf(
            "SELECT * FROM `%s` WHERE FIND_IN_SET(`id`, :ids);",
            $this->settings->TABLES['LM_LEADS']
        ), ['ids' => implode(',', $reqLeads)]);

        if (empty($leads)) {
            throw new NotFoundException('Failed to find a lead with the requested IDs.');
        }
        $leadsObjects = [];
        foreach($leads as $lead) {
            $leadsObjects[] = new Backend_Lead($lead);
        }

        // Load the Lead Objects
        return $leadsObjects;
    }
}
