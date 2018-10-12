<?php

namespace REW\Api\Internal\Controller\Route\Crm\Leads\Lead\Phone;

use REW\Api\Internal\Exception\BadRequestException;
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
use \History_Event_Phone_Attempt;
use \History_Event_Phone_Contact;
use \History_Event_Phone_Invalid;
use \History_Event_Phone_Voicemail;
use \History_User_Lead;
use \Hooks;

/**
 * Lead Track Phonecall Controller
 * @package REW\Api\Internal\Controller
 */
class Track implements ControllerInterface
{
    const CALL_TYPES = ['call', 'attempt', 'voicemail', 'invalid'];

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var string
     */
    protected $callMethod;

    /**
     * @var Backend_Lead
     */
    protected $lead;

    /**
     * @var array
     */
    protected $post;

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
        $body = json_decode($request->getBody());
        $this->post = (!empty($body) ? (array) $body : []);
        $this->routeParams = $routeParams;

        $this->lead = $this->fetchLead();

        $this->checkRequestValidity();

        $this->setCallHistoryMethod();

        $this->trackCall();
    }

    /**
     * @throws BadRequestException
     * @throws InsufficientPermissionsException
     */
    protected function checkRequestValidity()
    {
        $leadAuth = new LeadAuth($this->settings, $this->auth, $this->lead);

        if (!$leadAuth->canManageLead()) {
            throw new InsufficientPermissionsException('You do not have permission to manage this lead.');
        }

        if (empty($this->post['type']) || !in_array($this->post['type'], self::CALL_TYPES)) {
            throw new BadRequestException('Invalid call response type.');
        }

        if (empty($this->post['details'])) {
            throw new BadRequestException('Call details is a required value.');
        }
    }

    /**
     * Fetch the requested lead
     * @throws NotFoundException
     * @return Backend_Lead
     */
    protected function fetchLead()
    {
        $lead = $this->db->fetch(sprintf(
            "SELECT * FROM `%s` WHERE `id` = :id;",
            $this->settings->TABLES['LM_LEADS']
        ), ['id' => $this->routeParams['leadId']]);

        $lead = new Backend_Lead($lead);

        if (empty($lead)) {
            throw new NotFoundException('Failed to find a lead with the requested ID.');
        }

        return $lead;
    }

    /**
     * Set the history tracking class based on the call response
     */
    protected function setCallHistoryMethod()
    {
        switch ($this->post['type']) {
            // Talked to Lead
            case 'call':
                $this->callMethod = 'History_Event_Phone_Contact';
                break;
            // Call Attempt
            case 'attempt':
                $this->callMethod = 'History_Event_Phone_Attempt';
                break;
            // Received Voicemail / Left Message
            case 'voicemail':
                $this->callMethod = 'History_Event_Phone_Voicemail';
                break;
            // Bad Phone Number
            case 'invalid':
                $this->callMethod = 'History_Event_Phone_Invalid';
                break;
        }
    }

    /**
     * @throws ServerSuccessException
     */
    protected function trackCall()
    {
        // Log Event: Track Phone Call
        $event = new $this->callMethod([
            'details' => $this->post['details']
        ], [
            new History_User_Lead($this->lead->getId()),
            $this->auth->getHistoryUser()
        ]);

        // Save to DB
        $event->save($this->db);

        // Run hook
        Hooks::hook(Hooks::HOOK_AGENT_CALL_OUTGOING)->run(
            $this->auth->getInfo(),
            (array) $this->lead,
            $this->post['type'],
            $this->post['details']
        );

        throw new ServerSuccessException('The phone call has been tracked successfully.');
    }
}
