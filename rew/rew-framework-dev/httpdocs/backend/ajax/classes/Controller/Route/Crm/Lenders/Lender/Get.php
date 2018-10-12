<?php

namespace REW\Api\Internal\Controller\Route\Crm\Lenders\Lender;

use REW\Api\Internal\Exception\InsufficientPermissionsException;
use REW\Api\Internal\Exception\NotFoundException;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Api\Internal\FormatFactory;
use REW\Backend\Auth\LendersAuth;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use Slim\Http\Response;
use Slim\Http\Request;

/**
 * Lender Get Controller
 * @package REW\Api\Internal\Controller
 */
class Get implements ControllerInterface
{
    /**
     * This endpoint's supported request parameters
     */
    const PERMITTED_REQUEST_FIELDS = [
        'standard' => [
            'address',
            'auth',
            'cell_phone',
            'city',
            'default_filter',
            'default_order',
            'default_sort',
            'email',
            'fax',
            'first_name',
            'home_phone',
            'id',
            'last_name',
            'office_phone',
            'page_limit',
            'state',
            'timezone',
            'type',
            'zip',
        ],
        'boolean' => [
            'auto_assign_admin',
            'auto_assign_optin',
        ],
        'timestamp' => [
            'auto_assign_time',
            'last_logon',
            'timestamp_created',
            'timestamp_reset',
            'timestamp_updated',
        ],
    ];

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
     * @var array
     */
    protected $lender;

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

        $this->lender = $this->fetchLender();

        $this->checkRequestValidity();

        $body = $this->getResponse();
        $response->setBody(json_encode($body));
    }

    /**
     * Check request validity
     * @throws InsufficientPermissionsException
     * @throws NotFoundException
     */
    protected function checkRequestValidity()
    {
        $lenderAuth = new LendersAuth($this->settings);
        if (!$lenderAuth->canViewLenders($this->auth)) {
            throw new InsufficientPermissionsException('You do not have the proper CRM permissions to perform this request.');
        }

        if (empty($this->lender)) {
            throw new NotFoundException('Failed to locate a lender with the requested ID.');
        }
    }

    /**
     * Fetch the requested lender
     *
     * @return array
     */
    protected function fetchLender()
    {
        $lender = $this->db->fetch(sprintf(
            "SELECT "
            . " * "
            . " FROM `%s` `l` "
            . " LEFT JOIN `%s` `au` ON `au`.`id` = `l`.`auth` "
            . " WHERE `l`.`id` = :id "
            . ";",
            $this->settings->TABLES['LM_LENDERS'],
            $this->settings->TABLES['LM_AUTH']
        ), [
            'id' => $this->routeParams['lenderId']
        ]);
        return $lender;
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
        $response = array_merge(
            FormatFactory::create('boolean', $this->lender, self::PERMITTED_REQUEST_FIELDS['boolean'], $requestFields)->format(),
            FormatFactory::create('standard', $this->lender, self::PERMITTED_REQUEST_FIELDS['standard'], $requestFields)->format(),
            FormatFactory::create('timestamp', $this->lender, self::PERMITTED_REQUEST_FIELDS['timestamp'], $requestFields)->format()

        );
        ksort($response);

        return $response;
    }
}
