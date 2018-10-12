<?php

namespace REW\Api\Internal\Controller\Route\Crm\Leads\Lead\Text;

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
use \Exception;

/**
 * Lead Text Verify Controller
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
     */
    public function __invoke(Request $request, Response $response, $routeParams = [])
    {
        $this->routeParams = $routeParams;

        $this->lead = $this->fetchlead();

        $this->checkPermissions();

        $body = $this->verifyTextAddress();
        $response->setBody(json_encode($body));
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
     * Check if a phone number has been provided
     * @return bool
     */
    protected function checkTextAddress()
    {
        return ((!empty($this->lead->info('phone'))) ?: (!empty($this->lead->info('phone_cell'))));
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
     * Verify the targeted text # and Return response
     * @return array
     */
    protected function verifyTextAddress()
    {
        $response = [
            'lead' => [
                'first_name' => $this->lead->info('first_name'),
                'id' => $this->lead->getId(),
                'last_name' => $this->lead->info('last_name'),
                'opted_in' => $this->checkOptIn(),
                'phone' => ($this->lead->info('phone') ?: $this->lead->info('phone_cell')),
                'verification_error' => null,
                'verified' => false
            ],
        ];

        if (!$this->checkTextAddress()) {
            $response['lead']['verification_error'] = 'This lead does not have a phone #.';
        } else {
            try {
                // Send to this number
                $to = ($this->lead->info('phone') ?: $this->lead->info('phone_cell'));

                // Validate phone number
                $phone_check = $this->lead->validateCellNumber($to);

                if (!empty($phone_check['verified'])) {
                    $response['lead']['verified'] = true;
                } else {
                    $response['lead']['verification_error'] = 'This lead\'s phone # has not been verified.';
                }
            // Invalid phone number
            } catch (Exception $e) {
                $response['lead']['verification_error'] = 'The lead\'s phone # is invalid.';
            }
        }

        return $response;
    }
}
