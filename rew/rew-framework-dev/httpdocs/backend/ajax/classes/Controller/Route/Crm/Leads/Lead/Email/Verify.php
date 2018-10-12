<?php

namespace REW\Api\Internal\Controller\Route\Crm\Leads\Lead\Email;

use REW\Api\Internal\Exception\InsufficientPermissionsException;
use REW\Api\Internal\Exception\NotFoundException;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Backend\Auth\Leads\LeadAuth;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use Slim\Http\Response;
use Slim\Http\Request;
use \Backend_Lead;

/**
 * Lead Email Verify Controller
 * @package REW\Api\Internal\Controller
 */
class Verify implements ControllerInterface
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
    protected $get;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @param AuthInterface $auth
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
     */
    public function __invoke(Request $request, Response $response, $routeParams = [])
    {
        $this->get = $request->get();
        $this->routeParams = $routeParams;

        $this->lead = $this->fetchLead();

        $this->checkPermissions();

        $body = $this->verifyRecipientAddress();
        $response->setBody(json_encode($body));
    }

    /**
     * @throws InsufficientPermissionsException
     */
    protected function checkPermissions()
    {
        $leadAuth = new LeadAuth($this->settings, $this->auth, $this->lead);
        if (!$leadAuth->canEmailLead()) {
            throw new InsufficientPermissionsException('You do not have permission to email this lead');
        }
    }

    /**
     * Fetch the requested lead
     * @throws NotFoundException
     * @return Backend_Lead
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

    /**
     * @return array
     */
    protected function verifyRecipientAddress()
    {
        $response = [
            'lead' => [
                'email' => $this->lead->info('email'),
                'first_name' => $this->lead->info('first_name'),
                'id' => $this->lead->getId(),
                'last_name' => $this->lead->info('last_name'),
                'reason_not_verified' => null,
                'verified' => false
            ]
        ];

        if ($this->lead->info('opt_marketing') !== 'in') {
            $response['lead']['reason_not_verified'] = 'This lead has unsubscribed from marketing mail.';
        } else if ($this->lead->info('bounced') === 'true') {
            $response['lead']['reason_not_verified'] = 'This lead has previously bounced an email.';
        } else if ($this->lead->info('fbl') === 'true') {
            $response['lead']['reason_not_verified'] = 'This lead has marked a previous message as spam.';
        } else {
            $response['lead']['verified'] = true;
        }

        return $response;
    }
}