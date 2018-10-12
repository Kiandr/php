<?php

namespace REW\Api\Internal\Controller\Route\Crm\Leads\Lead;

use REW\Api\Internal\Exception\InsufficientPermissionsException;
use REW\Api\Internal\Exception\NotFoundException;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Api\Internal\Store\Leads as LeadStore;
use REW\Api\Internal\Store\Listings as ListingStore;
use REW\Backend\Auth\Leads\LeadAuth;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Core\Interfaces\SettingsInterface;
use Slim\Http\Response;
use Slim\Http\Request;
use \Backend_Lead;
use \Exception;

/**
 * Lead Latest Listing Activity Get Controller
 * @package REW\Api\Internal\Controller
 */
class Listings implements ControllerInterface
{
    /**
     * @var array
     */
    const LISTING_REQUEST_TYPES = [
        'selling' => ['Seller Form','CMA Form','Radio Seller Form','Guaranteed Sold Form'],
        'showing' => ['Property Showing','Quick Showing']
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
     * @var LeadStore
     */
    protected $leadStore;

    /**
     * @var ListingStore
     */
    protected $listingStore;

    /**
     * @var LogInterface
     */
    protected $log;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @param AuthInterface $auth
     * @param DBInterface $db
     * @param LeadStore $leadStore
     * @param ListingStore $listingStore
     * @param LogInterface $log
     * @param SettingsInterface $settings
     */
    public function __construct(
        AuthInterface $auth,
        DBInterface $db,
        LeadStore $leadStore,
        ListingStore $listingStore,
        LogInterface $log,
        SettingsInterface $settings
    ) {
        $this->auth = $auth;
        $this->db = $db;
        $this->leadStore = $leadStore;
        $this->listingStore = $listingStore;
        $this->log = $log;
        $this->settings = $settings;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $routeParams
     * @throws NotFoundException If requested lead not found
     */
    public function __invoke(Request $request, Response $response, $routeParams = [])
    {
        try {
            $lead = $this->leadStore->getLead($routeParams['leadId']);
        } catch (Exception $e) {
            throw new NotFoundException($e->getMessage());
        }

        $this->checkRequestValidity($lead);

        $body = $this->getResponse($lead);
        $response->setBody(json_encode($body));
    }

    /**
     * Build filters for form mode requests
     *
     * @param Backend_Lead $lead
     * @param string $mode
     * @return array
     */
    protected function getFormSubmissionData(Backend_Lead $lead, $mode)
    {
        $params = [
            'leadId' => $lead->info('id')
        ];
        $filter = ' WHERE `user_id` = :leadId ';

        // Filter by request mode if applicable
        switch ($mode) {
            case 'inquiry':
                $filter .= " AND `form` NOT IN('" . implode("','", self::LISTING_REQUEST_TYPES['selling']) . "','" . implode("','", self::LISTING_REQUEST_TYPES['showing']) . "') "
                    . " AND `data` NOT LIKE :form_type "
                    . " AND `data` NOT LIKE :form_type_two ";
                $params['form_type'] = '%s:12:\"inquire_type\";s:16:\"Property Showing\";%';
                $params['form_type_two'] = '%s:12:\"inquire_type\";s:7:\"Selling\";%';
                break;
            case 'showing':
                $filter .= " AND (`form` IN ('" . implode("','", self::LISTING_REQUEST_TYPES['showing']) . "') OR ( "
                    . " `form` = 'IDX Inquiry' "
                    . " AND `data` LIKE :form_type "
                    . " )) ";
                $params['form_type'] = '%s:12:\"inquire_type\";s:16:\"Property Showing\";%';
                break;
        }

        // Exclusively listing inquiries/requests - IE: no general contact form submissions
        $filter .= " AND `data` LIKE '%%ListingMLS%%' ";

        // Get the Count + the newest MLS listing's data
        $sql = sprintf(
            'SELECT ('
                . ' SELECT COUNT(`id`) AS `total` '
                . ' FROM `%1$s` '
                . ' %2$s '
            . ' ) AS `total`, `data` '
            . ' FROM `%1$s` '
            . ' %2$s '
            . ' ORDER BY `timestamp` DESC ',
            $this->settings->TABLES['LM_USER_FORMS'],
            $filter
        );
        $result = $this->db->fetch($sql, $params);
        $data = unserialize($result['data']);

        // Get the listing data
        if (!empty($data['ListingFeed']) && !empty($data['ListingMLS'])) {
            $last_listing = $this->getListingData(
                $data['ListingFeed'],
                $data['ListingMLS']
            );
        }

        return $this->getListingDataResponse(
            (int) $result['total'],
            $last_listing ?: null
        );
    }

    /**
     * @param Backend_Lead $lead
     * @return array
     */
    protected function getListingFavorites(Backend_Lead $lead)
    {
        $sql_where = " WHERE `user_id` = :user_id "
            . " AND `agent_id` IS NULL "
            . " AND `associate` IS NULL ";

        $result = $this->db->fetch(sprintf(
            'SELECT ('
                . ' SELECT COUNT(`id`) AS `total` '
                . ' FROM `%1$s` '
                . ' %2$s '
            . ' ) AS `total`, `idx`, `mls_number` '
            . ' FROM `%1$s` '
            . ' %2$s '
            . ' ORDER BY `timestamp` DESC '
            . ';',
            $this->settings->TABLES['LM_USER_LISTINGS'],
            $sql_where
        ), [
            'user_id' => $lead->info('id')
        ]);

        if (!empty($result['idx']) && !empty($result['mls_number'])) {
            $last_listing = $this->getListingData(
                $result['idx'],
                $result['mls_number']
            );
        }

        return $this->getListingDataResponse(
            (int) $result['total'],
            $last_listing ?: null
        );
    }

    /**
     * @param Backend_Lead $lead
     * @return array
     */
    protected function getListingRecommendations(Backend_Lead $lead)
    {
        $sql_where = " WHERE `user_id` = :user_id "
            . " AND (`agent_id` IS NOT NULL OR `associate` IS NOT NULL) ";

        $result = $this->db->fetch(sprintf(
            'SELECT ('
                . ' SELECT COUNT(`id`) as `total` '
                . ' FROM `%1$s` '
                . ' %2$s '
            . ' ) AS `total`, `idx`, `mls_number` '
            . ' FROM `%1$s` '
            . ' %2$s '
            . ' ORDER BY `timestamp` DESC '
            . ';',
            $this->settings->TABLES['LM_USER_LISTINGS'],
            $sql_where
        ), [
            'user_id' => $lead->info('id')
        ]);

        if (!empty($result['idx']) && !empty($result['mls_number'])) {
            $last_listing = $this->getListingData(
                $result['idx'],
                $result['mls_number']
            );
        }

        return $this->getListingDataResponse(
            (int) $result['total'],
            $last_listing ?: null
        );
    }

    /**
     * @param Backend_Lead $lead
     * @return array
     */
    protected function getListingViews(Backend_Lead $lead)
    {
        $sql_where = ' WHERE `user_id` = :user_id ';

        $result = $this->db->fetch(sprintf(
            'SELECT ('
                . ' SELECT COUNT(`id`) as `total` '
                . ' FROM `%1$s` '
                . ' %2$s '
            . ' ) AS `total`, `idx`, `mls_number` '
            . ' FROM `%s` '
            . ' %2$s '
            . ' ORDER BY `timestamp` DESC '
            . ';',
            $this->settings->TABLES['LM_USER_VIEWED_LISTINGS'],
            $sql_where
        ), [
            'user_id' => $lead->info('id')
        ]);

        if (!empty($result['idx']) && !empty($result['mls_number'])) {
            $last_listing = $this->getListingData(
                $result['idx'],
                $result['mls_number']
            );
        }

        return $this->getListingDataResponse(
            (int) $result['total'],
            $last_listing ?: null
        );
    }

    /**
     * @param string $feed
     * @param string $mls_number
     * @return array
     */
    protected function getListingData($feed, $mls_number)
    {
        if (!empty($feed) && !empty($mls_number)) {
            $listing_fields = ['ListingMLS', 'ListingImage'];
            $result = $this->listingStore->getListing($feed, $mls_number, $listing_fields);
        }

        // No Listing Data Found
        if (empty($result)) {
            return [];
        }

        return [
            'feed' => $feed,
            'image_url' => $result['ListingImage'],
            'mls_number' => $mls_number,
        ];
    }

    /**
     * @param int $count
     * @param array|null $listing
     * @return array
     */
    protected function getListingDataResponse($count, $listing = null)
    {
        return [
            'last_listing' => $listing ? [
                'feed' => ($listing['feed'] ?: null),
                'image_url' => ($listing['image_url'] ?: null),
                'mls_number' => ($listing['mls_number'] ?: null)
            ] : null,
            'count' => (int) $count
        ];
    }

    /**
     * Check request validity
     * @param Backend_Lead $lead
     * @throws InsufficientPermissionsException
     */
    protected function checkRequestValidity(Backend_Lead $lead)
    {
        $leadsAuth = new LeadAuth($this->settings, $this->auth, $lead);
        if (!$leadsAuth->canViewLead($this->auth)) {
            throw new InsufficientPermissionsException('You do not have the proper CRM permissions to perform this request.');
        }
    }

    /**
     * Build the response
     *
     * @param Backend_Lead $lead
     * @return array
     */
    protected function getResponse(Backend_Lead $lead)
    {
        return [
            'favorites' => $this->getListingFavorites($lead),
            'inquiries' => $this->getFormSubmissionData($lead, 'inquiry'),
            'recommended' => $this->getListingRecommendations($lead),
            'showings' => $this->getFormSubmissionData($lead, 'showing'),
            'views' => $this->getListingViews($lead)
        ];
    }
}
