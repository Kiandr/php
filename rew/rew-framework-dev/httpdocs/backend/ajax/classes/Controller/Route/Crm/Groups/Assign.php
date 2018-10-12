<?php

namespace REW\Api\Internal\Controller\Route\Crm\Groups;

use REW\Api\Internal\Exception\BadRequestException;
use REW\Api\Internal\Exception\NotFoundException;
use REW\Api\Internal\Exception\ServerSuccessException;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\HooksInterface;
use Slim\Http\Response;
use Slim\Http\Request;
use \Backend_Lead;
use \Backend_Agent;
use \Backend_Group;
use \Hooks;

/**
 * Lead Group Assign Controller
 * @package REW\Api\Internal\Controller
 */
class Assign implements ControllerInterface
{
    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var array
     */
    protected $groups;

    /**
     * @var Backend_Lead
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

        $this->groups = $this->fetchGroups();

        $this->assignGroups();
    }

    /**
     * Assign the requested lead to the requested groups
     *
     * @throws ServerSuccessException
     * @return void
     */
    protected function assignGroups()
    {
        foreach ($this->groups as $group) {
            foreach ($this->leads as $lead) {
                $lead->assignGroup($group, $this->auth);
            }
        }
		
        $this->syncPartner($this->leads);
        // If we've made it this far the call was successful
        throw new ServerSuccessException('The lead has been successfully assigned to the requested groups.');
    }

    /**
     * Sync group assignment with third parties
     *
     * @param array
     * @return void
     */
    private function syncPartner($leads)
    {  	
    	//Collect assigned Agents
    	$agentList = array();
    	
    	//Get a hook instance 
    	$hook = Hooks::hook(Hooks::HOOK_LEAD_SYNC_PARTNER_UPDATING);
    	
    	foreach($leads as $lead){  	
    		
    		$agentId = $lead->info('agent');	
    		
    		if(!isset($agentList[$agentId])){
    			$agentList[$agentId] = Backend_Agent::load($agentId);
    		}
    		
    		$groups = Backend_Group::getGroups($errors, Backend_Group::LEAD, $lead->getId());
    		
    		$hook->run($lead, $agentList[$agentId], $groups);
    	}
    	
    }
    /**
     * @throws BadRequestException
     * @return array
     */
    protected function fetchGroups()
    {
        // Requested Group IDs
        $reqGroups = (!empty($this->post['group_ids']) && is_array($this->post['group_ids'])) ? $this->post['group_ids'] : [];

        // Pull/Check Requested Groups
        $groups = $this->db->fetchAll(sprintf(
            "SELECT * FROM `%s` WHERE FIND_IN_SET(`id`, :ids);",
            $this->settings->TABLES['LM_GROUPS']
        ), [
            'ids' => implode(',', $reqGroups)
        ]);

        if (empty($groups)) {
            throw new BadRequestException('Failed to locate any groups with the requested IDs.');
        }

        return $groups;
    }

    /**
     * @throws NotFoundException
     * @return array
     */
    protected function fetchLeads()
    {
        $reqLeads = (!empty($this->post['lead_ids']) && is_array($this->post['lead_ids'])) ? $this->post['lead_ids'] : [];

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
