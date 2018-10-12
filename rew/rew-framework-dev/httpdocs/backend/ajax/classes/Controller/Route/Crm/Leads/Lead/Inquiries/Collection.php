<?php

namespace REW\Api\Internal\Controller\Route\Crm\Leads\Lead\Inquiries;

use REW\Api\Internal\Exception\InsufficientPermissionsException;
use REW\Api\Internal\Exception\NotFoundException;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Api\Internal\Store\Leads as LeadStore;
use REW\Api\Internal\Store\Listings as ListingStore;
use REW\Api\Internal\Store\Leads;
use REW\Backend\Auth\Leads\LeadAuth;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use Slim\Http\Response;
use Slim\Http\Request;
use \Backend_Lead;
use \Exception;
use \Util_IDX;

/**
 * Lead Listing Activity Tracking Get Controller
 * @package REW\Api\Internal\Controller
 */
class Collection implements ControllerInterface
{
    /**
     * @var array
     */
    const LISTING_REQUEST_TYPES = [
        'selling' => ['Seller Form','CMA Form','Radio Seller Form','Guaranteed Sold Form'],
        'showing' => ['Property Showing','Quick Showing']
    ];

    /**
     * @var array
     */
    const SELECT_COLUMNS = [
        'ListingMLS',
        'ListingPrice',
        'ListingImage',
        'Address',
        'AddressCity',
        'AddressState',
        'NumberOfBedrooms',
        'NumberOfBathrooms',
        'NumberOfSqFt',
        'NumberOfBedrooms'
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
     * @var Leads
     */
    protected $leadStore;

    /**
     * @var ListingStore
     */
    protected $listingStore;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @param AuthInterface $auth
     * @param DBInterface $db
     * @param LeadStore $leadStore
     * @param ListingStore $listingStore
     * @param SettingsInterface $settings
     */
    public function __construct(
        AuthInterface $auth,
        DBInterface $db,
        LeadStore $leadStore,
        ListingStore $listingStore,
        SettingsInterface $settings
    ) {
        $this->auth = $auth;
        $this->db = $db;
        $this->leadStore = $leadStore;
        $this->listingStore = $listingStore;
        $this->settings = $settings;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $routeParams
     */
    public function __invoke(Request $request, Response $response, $routeParams = [])
    {
        try {
            $lead = $this->leadStore->getLead($routeParams['leadId']);
        } catch (Exception $e) {
            throw new NotFoundException($e->getMessage());
        }

        $this->checkRequestValidity($lead);

        $body = $this->getResponse($lead, $routeParams['type']);
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
        $filter = ' `user_id` = :leadId ';

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

        // Get the Count + the newest MLS listing's data
        $sql = sprintf(
            "SELECT `data`, "
            . "`timestamp`, "
            . "`form` "
            . " FROM `%s` "
            . " WHERE %s "
            // Exclusively listing inquiries/requests - IE: no general contact form submissions
            . " AND `data` LIKE '%%ListingMLS%%' "
            . " ORDER BY `timestamp` DESC ",
            $this->settings->TABLES['LM_USER_FORMS'],
            $filter
        );
        $results = $this->db->fetchAll($sql, $params);

        // Get the listing data
        $listings = $this->getListingData($results);

        return $listings;
    }

    /**
     * @param array $results
     * @return array
     */
    protected function getListingData($results)
    {
        $listings = [];
        $timestamps = [];
        $comments = [];
        $forms = [];
        $feeds = [];
        foreach($results as $result){
            $data = unserialize($result['data']);
            $feeds[$data['ListingFeed']][] = $data['ListingMLS'];
            $timestamps[$data['ListingMLS']] = $result['timestamp'];
            $forms[$data['ListingMLS']] = $result['form'];
            $comments[$data['ListingMLS']] = $data['comments'];
        }
        foreach($feeds as $feed => $mlsNumbers) {
            $results = $this->listingStore->getListings($feed, $mlsNumbers, self::SELECT_COLUMNS);
            foreach($results as $result) {
                $result['timestamp'] = $timestamps[$result['ListingMLS']];
                $result['comments'] = $comments[$result['ListingMLS']];
                $result['form'] = $forms[$result['ListingMLS']];
                $result['url'] = ($feed === 'cms' ? '/listing/cms/' . $result['ListingMLS'] : '/listing/' . $result['ListingMLS']);
                $listings[] = $result;
            }
        }

        return $listings;
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
     * @param String $type
     * @return array
     */
    protected function getResponse(Backend_Lead $lead, $type)
    {
        if ($type == 'inquiries') $response = $this->getFormSubmissionData($lead, 'inquiry');
        if ($type == 'showings')  $response = $this->getFormSubmissionData($lead, 'showing');

        return $response ?: [];
    }
}