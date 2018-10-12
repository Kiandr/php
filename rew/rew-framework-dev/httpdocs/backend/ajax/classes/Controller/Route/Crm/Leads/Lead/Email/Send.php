<?php

namespace REW\Api\Internal\Controller\Route\Crm\Leads\Lead\Email;

use REW\Api\Internal\Exception\BadRequestException;
use REW\Api\Internal\Exception\InsufficientPermissionsException;
use REW\Api\Internal\Exception\NotFoundException;
use REW\Api\Internal\Exception\ServerSuccessException;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Backend\Auth\Leads\LeadAuth;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Core\Interfaces\SettingsInterface;
use Slim\Http\Response;
use Slim\Http\Request;
use \Backend_Lead;
use \Backend_Mailer;
use \Exception;
use \History_Event_Email_Sent;
use \History_User_Lead;
use \Validate;

/**
 * Lead Email Send Controller
 * @package REW\Api\Internal\Controller
 */
class Send implements ControllerInterface
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
     * @var FormatInterface
     */
    protected $format;

    /**
     * @var Backend_Lead
     */
    protected $lead;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @param AuthInterface $auth
     * @param DBInterface $db
     * @param FormatInterface $format
     * @param SettingsInterface $settings
     */
    public function __construct(
        AuthInterface $auth,
        DBInterface $db,
        FormatInterface $format,
        SettingsInterface $settings
    ) {
        $this->auth = $auth;
        $this->db = $db;
        $this->format = $format;
        $this->settings = $settings;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $routeParams
     */
    public function __invoke(Request $request, Response $response, $routeParams = [])
    {
        $body = json_decode($request->getBody());
        $this->post = (!empty($body) ? (array) $body : []);
        $this->routeParams = $routeParams;

        $this->checkRequestValidity();

        $this->lead = $this->fetchLead();

        $this->checkPermissions();

        $this->sendEmail();
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
     * @throws BadRequestException
     */
    protected function checkRequestValidity()
    {
        if (empty($this->post['subject']) || empty($this->post['content'])) {
            throw new BadRequestException('Email subject and content are required fields');
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
     * Email the requested lead
     * @throws BadRequestException
     * @throws ServerSuccessException
     */
    protected function sendEmail()
    {
        // Set up Mailer
        $mailer = new Backend_Mailer([
            'html'        => true,
            'subject'     => $this->post['subject'],
            'message'     => $this->post['content'],
        ]);
        $mailer->setSender($this->auth->info('email'), $this->auth->getName());
        $mailer->setRecipient($this->lead->info('email'), $this->lead->getName());

        // Add requested CCs
        if (!empty($this->post['ccs']) && is_array($this->post['ccs'])) {
            foreach ($this->post['ccs'] as $cc) {
                if (Validate::email($cc, true)) {
                    $mailer->getMailer()->addCC($cc);
                }
            }
        }

        // Add requested BCCs
        if (!empty($this->post['bccs']) && is_array($this->post['bccs'])) {
            foreach ($this->post['bccs'] as $bcc) {
                if (Validate::email($bcc, true)) {
                    $mailer->getMailer()->addBCC($bcc);
                }
            }
        }

        // Try to send the email
        try {
            $sent = $mailer->Send([
                'email'      => $this->lead->info('email'),
                'first_name' => $this->lead->info('first_name'),
                'last_name'  => $this->lead->info('last_name'),
                'signature'  => $this->auth->info('signature'),
                'verify'     => sprintf(
                    '%sverify.html?verify=%s',
                    $this->settings->SETTINGS['URL_IDX'],
                    $this->format->toGuid($this->lead->info('guid'))
                )
            ]);
        } catch (Exception $e) {
            throw new BadRequestException(sprintf('Failed to send email: %s', $e->getMessage()));
        }

        // Send response based on success of sending the email
        if ($sent) {

            // Log Event: Track Phone Call
            $event = new History_Event_Email_Sent([
                'plaintext' => !$mailer->isHTML(),
                'subject' => $mailer->getSubject(),
                'message' => $mailer->getMessage(),
                'tags' => $mailer->getTags(),
                'sender' => $this->auth->info('id')
            ], [
                new History_User_Lead($this->lead->info('id')),
                $this->auth->getHistoryUser()
            ]);

            // Save to DB
            $event->save($this->db);

            throw new ServerSuccessException('The email has been sent successfully');
        } else {
            throw new BadRequestException('Failed to send email');
        }
    }
}
