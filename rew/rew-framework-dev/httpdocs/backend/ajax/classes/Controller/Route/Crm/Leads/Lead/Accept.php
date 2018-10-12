<?php

namespace REW\Api\Internal\Controller\Route\Crm\Leads\Lead;

use REW\Api\Internal\Exception\BadRequestException;
use REW\Api\Internal\Exception\ServerSuccessException;
use REW\Api\Internal\Exception\NotFoundException;
use REW\Api\Internal\Exception\InsufficientPermissionsException;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Api\Internal\Store\Leads;
use REW\Backend\Auth\Leads\LeadAuth;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Core\Interfaces\SettingsInterface;
use Slim\Http\Response;
use Slim\Http\Request;
use \Exception;

/**
 * Lead Accept Controller
 * @package REW\Api\Internal\Controller
 */
class Accept implements ControllerInterface
{
    /**
     * @var string
     */
    const ACCEPTED_STATUS = 'accepted';

    /**
     * @param AuthInterface $auth
     * @param DBInterface $db
     * @param LogInterface $log
     * @param SettingsInterface $settings
     */
    public function __construct(
        AuthInterface $auth,
        DBInterface $db,
        LogInterface $log,
        SettingsInterface $settings
    ) {
        $this->auth = $auth;
        $this->db = $db;
        $this->log = $log;
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
        $post = (!empty($body) ? (array) $body : []);

        try {
            $leadStore = new Leads($this->db, $this->settings);
            $lead = $leadStore->getLead($routeParams['leadId']);
        } catch (Exception $e) {
            $this->log->error($e->getMessage());
            throw new NotFoundException('Lead not found.');
        }

        $leadAuth = new LeadAuth($this->settings, $this->auth, $lead);

        if (!$leadAuth->canManageLead()) {
            throw new InsufficientPermissionsException('You do not have permission to accept this lead.');
        }

        if ($lead->info('status') === self::ACCEPTED_STATUS) {
            throw new BadRequestException('This lead is already accepted.');
        }

        $lead->status(self::ACCEPTED_STATUS);

        // If we've made it this far the call was successful
        throw new ServerSuccessException('The lead has been accepted successfully.');
    }
}
