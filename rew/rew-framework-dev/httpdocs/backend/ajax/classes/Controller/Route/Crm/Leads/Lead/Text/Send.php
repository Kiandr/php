<?php

namespace REW\Api\Internal\Controller\Route\Crm\Leads\Lead\Text;

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
use \History_Event_Text_Outgoing;
use \History_User_Lead;
use \Partner_Twilio;
use \Partner_Twilio_Exception;

/**
 * Lead Text Send Controller
 * @package REW\Api\Internal\Controller
 */
class Send implements ControllerInterface
{
    /**
     * @var int
     */
    const MAX_CONTENT_LENGTH = 160;

    /**
     * @var string
     */
    protected $phone_number;

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
    protected $from;

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
     * @var Partner_Twilio
     */
    protected $twilio;

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

        $this->lead = $this->fetchlead();

        $this->phone_number = ($this->post['phone_number']) ?: ( $this->lead->info('phone') ?: $this->lead->info('phone_cell'));

        $this->checkPermissions();

        $this->twilio = Partner_Twilio::getInstance();

        $this->verifyRequest();

        $this->sendText();
    }

    /**
     * Check if the lead has opted out of receiving text messages
     * @return bool
     */
    protected function checkOptIn()
    {
        return $this->lead->info('opt_texts') === 'in';
    }

    /**
     * Check the necessary permissions for this request
     * @throws InsufficientPermissionsException
     */
    protected function checkPermissions()
    {
        $leadAuth = new LeadAuth($this->settings, $this->auth, $this->lead);
        if (!$leadAuth->canTextLead()) {
            throw new InsufficientPermissionsException('You do not have permission to text this lead');
        }
    }

    /**
     * Check if a phone number is available
     * @return bool
     */
    protected function checkTextAddress()
    {
        return (!empty($this->phone_number));
    }

    /**
     * Fetch the requested lead
     * @throws NotFoundException
     * @return array
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
     * Send the text
     * @throws BadRequestException
     * @throws ServerSuccessException
     */
    protected function sendText()
    {
        $replace = [
            '{first_name}' => $this->lead->info('first_name'),
            '{last_name}' => $this->lead->info('last_name')
        ];
        $content = str_replace(
            array_keys($replace),
            array_values($replace),
            $this->post['content']
        );

        try {
            $this->twilio->sendSmsMessage(
                $this->phone_number,
                $this->from,
                $content
            );
        } catch (Exception $e) {
            throw new BadRequestException(sprintf('Failed to send text: %s', $e->getMessage()));
        }

        // Track outgoing text message
        (new History_Event_Text_Outgoing([
            'to'    => $this->phone_number,
            'from'  => $this->from,
            'body'  => $content
        ], [
            new History_User_Lead($this->lead->getId()),
            $this->auth->getHistoryUser()
        ]))->save();

        throw new ServerSuccessException('The text has been sent successfully.');
    }

    /**
     * Verify the targeted text # and body
     * @throws BadRequestException
     * @throws InsufficientPermissionsException
     */
    protected function verifyRequest()
    {
        // Check if the lead has a phone #
        if (!$this->checkTextAddress()) {
            throw new InsufficientPermissionsException('Failed to locate a target phone #');
        // Check if the lead is opted out of receiving texts
        } else if (!$this->checkOptIn()) {
            throw new InsufficientPermissionsException('This lead has unsubscribed from marketing texts');
        } else {
            // Validate phone number
            try {
                $phone_data = $this->lead->validateCellNumber($this->phone_number);
                // Store formatted phone number
                $this->phone_number = $phone_data['phone_number'];
            // Invalid phone number
            } catch (Exception $e) {
                throw new InsufficientPermissionsException(sprintf('Failed to validate lead phone #: %s', $e->getMessage()));
            }
        }

        // Check if a text body was provided
        if (empty($this->post['content'])) {
            throw new BadRequestException('No text message content provided.');
        }

        // Check if text body is too long
        if (strlen($this->post['content']) > self::MAX_CONTENT_LENGTH) {
            throw new BadRequestException(sprintf('Text message content exceeds limit: %d.', self::MAX_CONTENT_LENGTH));
        }

        // Check for and load the first available twilio number
        try {
            if (!$this->twilio) {
                throw new Partner_Twilio_Exception('Twilio texting is not set up on this site.');
            }
            $numbers = $this->twilio->getTwilioNumbers();
            $numbers = array_slice($numbers, 0, 1);
            if (!empty($numbers)) {
                $this->from = $numbers[0]['phone_number'];
            }
            if (empty($this->from)) {
                throw new InsufficientPermissionsException('Failed to load sender address: No sender numbers available.');
            }
        // No number(s) were available
        } catch (Partner_Twilio_Exception $e) {
            throw new InsufficientPermissionsException(sprintf('Failed to load sender address: %s', $e->getMessage()));
        }
    }
}
