<?php

namespace REW\Api\Internal\Controller\Route\Crm\Leads\Lead;

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

/**
 * Lead Delete Controller
 * @package REW\Api\Internal\Controller
 */
class Delete implements ControllerInterface
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
    protected $routeParams;

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

        $this->lead = $this->fetchLead();

        $this->checkPermissions();

        $this->deleteLead();
    }

    /**
     * Check authuser delete permission for this lead
     * @throws InsufficientPermissionsException
     */
    protected function checkPermissions()
    {
        $leadAuth = new LeadAuth($this->settings, $this->auth, $this->lead);

        if (!$leadAuth->canDeleteLead()) {
            throw new InsufficientPermissionsException('You do not have the proper CRM permission to delete this lead.');
        }
    }

    /**
     * Delete the lead
     * @throws ServerSuccessException
     */
    protected function deleteLead()
    {
        // Delete the Lead
        $this->lead->delete($this->auth);

        // If we've made it this far the call was successful
        throw new ServerSuccessException('The lead has been deleted successfully.');
    }

    /**
     * Fetch the requested lead
     * @return array
     * @throws NotFoundException
     */
    protected function fetchLead()
    {
        $result = $this->db->fetch(sprintf(
            "SELECT * FROM `%s` WHERE `id` = :id;",
            $this->settings->TABLES['LM_LEADS']
        ), ['id' => $this->routeParams['leadId']]);

        $lead = new Backend_Lead($result);

        if (empty($lead)) {
            throw new NotFoundException('Failed to find a lead with the requested ID.');
        }

        return $lead;
    }
}
