<?php

namespace REW\Api\Internal\Controller\Route\Crm\Leads\Lead\Lender;

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
use \Backend_Lead;
use \Backend_Lender;

/**
 * Lead Lender Assign Controller
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
     * @var Backend_Lead
     */
    protected $lead;

    /**
     * @var array
     */
    protected $lender;

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
        $this->routeParams = $routeParams;

        $this->checkLenderModule();

        $this->lead = $this->fetchLead();
        $this->lender = $this->fetchLender();

        $this->checkPermissions();

        $this->assignLender();
    }

    /**
     * @throws ServerSuccessException
     * @return void
     */
    protected function assignLender()
    {
        // Assign the Lead
        $this->lead->assign($this->lender, $this->auth);

        // Notify the Lender
        $this->lender->notifyLender([$this->lead], $this->auth);

        // If we've made it this far the call was successful
        throw new ServerSuccessException('The lead has been assigned successfully.');
    }

    /**
     * Check if the lenders module is active
     * @throws InsufficientPermissionsException
     */
    protected function checkLenderModule()
    {
        if (empty($this->settings->MODULES['REW_LENDERS_MODULE'])) {
            throw new InsufficientPermissionsException('The lender feature is not enabled on this site.');
        }
    }

    /**
     * Check the request's necessary permissions
     * @throws InsufficientPermissionsException
     */
    protected function checkPermissions()
    {
        // Check Request VS Permissions
        $leadAuth = new LeadAuth($this->settings, $this->auth, $this->lead);

        if (!$leadAuth->canManageLead()) {
            throw new InsufficientPermissionsException('You do not have the proper CRM permission to manage this lead.');
        }

        if (!$leadAuth->canAssignLenderToLead()) {
            throw new InsufficientPermissionsException('You do not have the proper CRM permission to assign this lead to a lender.');
        }
    }

    /**
     * @return Backend_Lead
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
     * @throws NotFoundException
     * @return array
     */
    protected function fetchLender()
    {
        // Check if Lender Exists
        $lender = $this->db->fetch(sprintf(
            "SELECT * FROM `%s` WHERE `id` = :id;",
            $this->settings->TABLES['LM_LENDERS']
        ), ['id' => $this->routeParams['lenderId']]);

        if (empty($lender)) {
            throw new NotFoundException('Failed to find a lender with the requested ID.');
        }

        // Load the Lender Object
        return Backend_Lender::load($lender['id']);
    }
}
