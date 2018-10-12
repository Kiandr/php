<?php

namespace REW\Api\Internal\Controller\Route\Crm\Leads\Lead;

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

/**
 * Lead Update Controller
 * @package REW\Api\Internal\Controller
 */
class Update implements ControllerInterface
{
    /**
     * @var array
     */
    const BASIC_FIELDS = [
        'address1',
        'address2',
        'address3',
        'city',
        'comments',
        'country',
        'email',
        'email_alt',
        'first_name',
        'keywords',
        'last_name',
        'notes',
        'origin',
        'phone',
        'phone_cell',
        'phone_fax',
        'phone_work',
        'remarks',
        'search_city',
        'search_maximum_price',
        'search_minimum_price',
        'search_subdivision',
        'search_type',
        'state',
        'zip',
    ];

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * DBInterface
     */
    protected $db;

    /**
     * @var array
     */
    protected $errorFields;

    /**
     * @var Backend_Lead
     */
    protected $lead;

    /**
     * @var LeadAuth
     */
    protected $leadAuth;

    /**
     * @var array
     */
    protected $put;

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

        $body = json_decode($request->getBody());
        $this->put = (!empty($body) ? (array) $body : []);

        $this->lead = $this->fetchLead();
        $this->leadAuth = new LeadAuth($this->settings, $this->auth, $this->lead);

        $this->checkPermissions();
        $this->clearDisallowedFields();

        $this->errorFields = [];

        $this->prepareUpdate();

        $this->executeUpdate();
    }

    /**
     * Check field-level authuser edit permission for this lead
     */
    protected function clearDisallowedFields()
    {
        // Disallow Specific Fields for Non-Permitted Users
        if (!$this->leadAuth->canManageLead()) {
            unset(
                $this->put['heat'],
                $this->put['status'],
                $this->put['rejectwhy'],
                $this->put['first_name'],
                $this->put['last_name'],
                $this->put['email'],
                $this->put['update_password'],
                $this->put['new_password'],
                $this->put['phone'],
                $this->put['groups'],
                $this->put['notify_favs'],
                $this->put['notify_searches'],
                $this->put['share_lead'],
                $this->put['action_plans']
            );
        }
    }

    /**
     * Check authuser edit permission for this lead
     * @throws InsufficientPermissionsException
     */
    protected function checkPermissions()
    {
        if (!$this->leadAuth->canEditLead()) {
            throw new InsufficientPermissionsException('You do not have the proper CRM permission to edit this lead.');
        }
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
        if (empty($result)) {
            throw new NotFoundException('Failed to find a lead with the requested ID.');
        }
        $lead = new Backend_Lead($result);

        return $lead;
    }

    /**
     * Finalize the request
     * @throws BadRequestException
     * @throws ServerSuccessException
     */
    protected function executeUpdate()
    {
        if (!empty($this->errorFields)) {
            // Error With at Least One Field - Don't Update the Lead
            throw new BadRequestException(sprintf('Failed to update lead - there were errors with the following fields: %s', implode(', ', $this->errorFields)));
        } else {
            // Update the Status if it was Requested and Deemed Valid Above
            if (!empty($this->put['status'])) {
                $this->lead->status($this->put['status']);
            }

            // Plug the Lead Object Data Into the DB Record
            $this->lead->save();

            // If we've made it this far the call was successful
            throw new ServerSuccessException('The lead has been updated successfully.');
        }
    }

    /**
     * Queue update requests into lead object
     */
    protected function prepareUpdate()
    {
        foreach(self::BASIC_FIELDS as $basicField) {
            if (!empty($this->put[$basicField])) {
                $this->lead->info($basicField, htmlspecialchars($this->put[$basicField]));
            }
        }

        // Permitted Request Values for Boolean Fields
        $boolVals = ['true','false'];

        // Shark Tank Designation
        if (!empty($this->put['in_shark_tank']) && $this->put['in_shark_tank'] !== $this->lead->info('in_shark_tank')) {
            if (in_array($this->put['in_shark_tank'], $boolVals)) {
                if ($this->put['in_shark_tank'] == 'true') {
                    $this->lead->info('timestamp_in_shark_tank', date('Y-m-d h:i:s', time()));
                } else {
                    $this->lead->info('timestamp_out_shark_tank', date('Y-m-d h:i:s', time()));
                }
                $this->lead->info('in_shark_tank', $this->put['in_shark_tank']);
            } else {
                $this->errorFields[] = 'in_shark_tank';
            }
        }

        // Agent Notifications
        if (!empty($this->put['notify_favs'])) {
            if (in_array($this->put['notify_favs'], $boolVals)) {
                $this->lead->info('notify_faves', ($this->put['notify_favs'] == 'true' ? 'yes' : 'no'));
            } else {
                $this->errorFields[] = 'notify_favs';
            }
        }
        if (!empty($this->put['notify_searches'])) {
            if (in_array($this->put['notify_searches'], $boolVals)) {
                $this->lead->info('notify_searches', ($this->put['notify_searches'] == 'true' ? 'yes' : 'no'));
            } else {
                $this->errorFields[] = 'notify_searches';
            }
        }

        // Share with Team
        if (!empty($this->put['share_lead'])) {
            if (in_array($this->put['share_lead'], $boolVals)) {
                $this->lead->info('share_lead', ($this->put['share_lead'] == 'true' ? '1' : '0'));
            } else {
                $this->errorFields[] = 'share_lead';
            }
        }

        // Lead Status
        if (!empty($this->put['status'])) {
            if (array_key_exists($this->put['status'], Backend_Lead::$statuses)) {
                if ($this->put['status'] == 'rejected') {
                    if (empty($this->put['rejectwhy'])) {
                        $this->errorFields[] = 'status';
                    } else {
                        $this->lead->info('rejectwhy', $this->put['rejectwhy']);
                    }
                }
            } else {
                $this->errorFields[] = 'status';
            }
        }

        // Lead Heat
        if (!empty($this->put['heat'])) {
            if (in_array($this->put['heat'], ['hot','mediumhot','warm','lukewarm','cold'])) {
                $this->lead->info('heat', $this->put['heat']);
            } else {
                $this->errorFields[] = 'heat';
            }
        }

        // Permitted Request Values for Phone Status Fields
        $phoneStatusVals = ['1','2','3','4','5','6','7'];
        $phoneStatusTypes = ['phone_home_status', 'phone_cell_status', 'phone_work_status'];

        // Phone Statuses
        foreach ($phoneStatusTypes as $type) {
            if (!empty($this->put[$type])) {
                if (in_array($this->put[$type], $phoneStatusVals)) {
                    $this->lead->info($type, $this->put[$type]);
                } else {
                    $this->errorFields[] = $type;
                }
            }
        }

        // Lead Contact Method
        if (!empty($this->put['contact_method'])) {
            if (in_array($this->put['contact_method'], ['email','phone','text'])) {
                $this->lead->info('contact_method', $this->put['contact_method']);
            } else {
                $this->errorFields[] = 'contact_method';
            }
        }
    }
}
