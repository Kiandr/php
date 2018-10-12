<?php

namespace REW\Api\Internal\Controller\Route\Crm\Leads\Lead\Note;

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
use \History_Event_Create_LeadNote;
use \History_User_Lead;
use \Hooks;

/**
 * Lead Add Note Controller
 * @package REW\Api\Internal\Controller
 */
class Add implements ControllerInterface
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

        $this->addNote();
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

        if (empty($this->post['content'])) {
            throw new BadRequestException('Note content is a required value.');
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
     * @throws Exception
     * @throws ServerSuccessException
     */
    protected function addNote()
    {
        // Add the note
        $query = $this->db->prepare(sprintf(
            "INSERT INTO `%s` SET "
            . ($this->auth->isAgent() ? " `agent_id` = :auth_id, " : "")
            . ($this->auth->isLender() ? " `lender` = :auth_id, " : "")
            . ($this->auth->isAssociate() ? " `associate` = :auth_id, " : "")
            . " `user_id` = :user_id, "
            . " `note` = :note, "
            . " `share` = :share, "
            . " `timestamp` = NOW() "
            . ";",
            $this->settings->TABLES['LM_USER_NOTES']
        ));
        $params = [
            'auth_id' => $this->auth->info('id'),
            'note' => $this->post['content'],
            'share' => (!empty($this->post['share']) ? 'true' : 'false'),
            'user_id' => $this->lead->getId(),
        ];
        if ($query->execute($params)) {
            // Log Event: Add Lead Note
            $event = new History_Event_Create_LeadNote([
                'details' => $this->post['content']
            ], [
                new History_User_Lead($this->lead->getId()),
                $this->auth->getHistoryUser()
            ]);

            // Save to DB
            $event->save($this->db);

            throw new ServerSuccessException('The note has been added successfully.');
        } else {
            throw new BadRequestException('Failed to track lead note.');
        }
    }

}