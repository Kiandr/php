<?php

namespace REW\Api\Internal\Controller\Route\Crm\Leads\Lead;

use REW\Api\Internal\Exception\BadRequestException;
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
 * Lead Reject Controller
 * @package REW\Api\Internal\Controller
 */
class Reject implements ControllerInterface
{
    /**
     * @var string
     */
    CONST REJECTED_STATUS = 'rejected';

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
     * @throws BadRequestException
     * @throws InsufficientPermissionsException
     * @throws NotFoundException
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
            throw new InsufficientPermissionsException('You do not have permission to reject this lead.');
        }

        if (strtolower($lead->info('status')) === self::REJECTED_STATUS) {
            throw new BadRequestException('This lead is already rejected.');
        }

        if (empty($post['reason'])) {
            throw new BadRequestException('You must supply a reason for rejecting this lead.');
        }

        $lead->info('rejectwhy', $post['reason']);
        $lead->status(self::REJECTED_STATUS, $this->auth);
        $lead->save();
    }
}
