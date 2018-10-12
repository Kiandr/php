<?php

namespace REW\Api\Internal\Controller\Route\Crm\Agents\Agent;

use REW\Api\Internal\Exception\InsufficientPermissionsException;
use REW\Api\Internal\Exception\NotFoundException;
use REW\Api\Internal\FormatFactory;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Backend\Auth\Agents\AgentAuth;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use Slim\Http\Response;
use Slim\Http\Request;
use \Backend_Agent;

/**
 * Agent Get Controller
 * @package REW\Api\Internal\Controller
 */
class Get implements ControllerInterface
{
    /**
     * This endpoint's supported request parameters
     */
    const PERMITTED_REQUEST_FIELDS = [
        'standard' => [
            'agent_id',
            'ar_bcc_email',
            'ar_cc_email',
            'ar_document',
            'ar_subject',
            'ar_tempid',
            'auth',
            'auto_assign_app_id',
            'auto_rotate_app_id',
            'blog_picture',
            'blog_profile',
            'blog_signature',
            'cell_phone',
            'cms_link',
            'cms_idxs',
            'default_filter',
            'default_order',
            'default_sort',
            'email',
            'fax',
            'first_name',
            'home_phone',
            'id',
            'image',
            'last_name',
            'notifications',
            'office',
            'office_phone',
            'page_limit',
            'remarks',
            'remax_launchpad_url',
            'remax_launchpad_username',
            'showing_suite_email',
            'signature',
            'sms_email',
            'timezone',
            'title',
            'type',
            'website',
        ],
        'boolean' => [
            'add_sig',
            'admin',
            'ar_active',
            'ar_is_html',
            'auto_assign_admin',
            'auto_assign_agent',
            'auto_optout',
            'auto_rotate',
            'auto_search',
            'blog',
            'blog_signature_on',
            'cms',
            'display',
            'display_feature',
            'google_calendar_sync',
            'microsoft_calendar_sync',
        ],
        'timestamp' => [
            'auto_assign_time',
            'auto_optout_time',
            'last_logon',
            'timestamp',
            'timestamp_created',
            'timestamp_reset',
            'timestamp_updated',
        ],
    ];

    /**
     * @var array
     */
    protected $agent;

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var DBInterface
     */
    protected $db;

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

        $this->get = $request->get();

        $this->agent = $this->fetchAgent($this->routeParams['agentId']);

        $this->checkRequestValidity();

        $response->setBody(json_encode($this->getResponse()));
    }

    /**
     * Build the response
     *
     * @return array
     */
    protected function getResponse()
    {
        // Allow user to request specific response fields
        $requestFields = (!empty($this->get['fields']) && is_array($this->get['fields']))
            ? $this->get['fields']
            : [];

        // Format Response Values
        $return = array_merge(
            FormatFactory::create('boolean', $this->agent, self::PERMITTED_REQUEST_FIELDS['boolean'], $requestFields)->format(),
            FormatFactory::create('standard', $this->agent, self::PERMITTED_REQUEST_FIELDS['standard'], $requestFields)->format(),
            FormatFactory::create('timestamp', $this->agent, self::PERMITTED_REQUEST_FIELDS['timestamp'], $requestFields)->format()
        );
        ksort($return);

        return $return ?: [];
    }

    /**
     * Check for adequate permissions, existing requested data
     * @throws InsufficientPermissionsException
     * @throws NotFoundException
     */
    protected function checkRequestValidity()
    {
        // Check if the agent exists
        if (empty($this->agent)) {
            throw new NotFoundException('Failed to locate an agent with the requested ID.');
        }

        // Check Permissions
        $agentAuth = new AgentAuth($this->settings, $this->auth, Backend_Agent::load($this->agent['id']));
        if (!$agentAuth->canViewAgent()) {
            throw new InsufficientPermissionsException('You do not have the proper CRM permissions to perform this request.');
        }
    }

    /**
     * Get the requested agent db row
     *
     * @param int $id
     * @return array
     */
    protected function fetchAgent($id)
    {
        $agent = $this->db->fetch(sprintf(
            "SELECT "
            . " * "
            . " FROM `%s` `a` "
            . " LEFT JOIN `%s` `au` ON `au`.`id` = `a`.`auth` "
            . " WHERE `a`.`id` = :id; "
            . ";",
            $this->settings->TABLES['LM_AGENTS'],
            $this->settings->TABLES['LM_AUTH']
        ), [
            'id' => $id,
        ]);

        // Prepend image with agent upload dir
        if (!empty($agent['image'])) {
            $agent['image'] = $this->settings->URLS['UPLOADS'] . 'agents/' . $agent['image'];
        }

        return $agent;
    }
}
