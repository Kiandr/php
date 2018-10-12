<?php

namespace REW\Api\Internal\Controller\Route\Crm\Leads\Lead\Listings;

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
use \UnexpectedValueException;
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
     * @param Backend_Lead $lead
     * @return array
     */
    protected function getListingFavorites(Backend_Lead $lead)
    {
        $results = $this->db->fetchAll(sprintf(
            "SELECT `idx`, "
            . "`mls_number` as ListingMLS, "
            . "`price` as ListingPrice,"
            . "`subdivision` as Address,"
            . "`city` as AddressCity,"
            . "`bedrooms` as NumberOfBedrooms,"
            . "`bathrooms` as NumberOfBathrooms,"
            . "`sqft` as NumberOfSqFt,"
            . "`timestamp` "
            . " FROM `%s` "
            . " WHERE `user_id` = :user_id "
            . " AND `agent_id` IS NULL "
            . " AND `associate` IS NULL "
            . ";",
            $this->settings->TABLES['LM_USER_LISTINGS']
        ), [
            'user_id' => $lead->info('id')
        ]);

        $listings = $this->getListingData($results,true);

        return $listings;
    }

    /**
     * @param Backend_Lead $lead
     * @return array
     */
    protected function getListingRecommendations(Backend_Lead $lead)
    {
        $results = $this->db->fetchAll(sprintf(
            "SELECT `idx`, "
            . "`mls_number` as ListingMLS, "
            . "`price` as ListingPrice,"
            . "`subdivision` as Address,"
            . "`city` as AddressCity,"
            . "`bedrooms` as NumberOfBedrooms,"
            . "`bathrooms` as NumberOfBathrooms,"
            . "`sqft` as NumberOfSqFt,"
            . "`timestamp` "
            . " FROM `%s` "
            . " WHERE `user_id` = :user_id "
            . " AND (`agent_id` IS NOT NULL OR `associate` IS NOT NULL) "
            . " ORDER BY `timestamp` DESC "
            . ";",
            $this->settings->TABLES['LM_USER_LISTINGS']
        ), [
            'user_id' => $lead->info('id')
        ]);

        $listings = $this->getListingData($results, true);

        return $listings;
    }

    /**
     * @param Backend_Lead $lead
     * @return array
     */
    protected function getListingViews(Backend_Lead $lead)
    {
        $results = $this->db->fetchAll(sprintf(
            "SELECT `idx`, `mls_number`, `timestamp`, "
            . "`mls_number` as ListingMLS "
            . " FROM `%s` "
            . " WHERE `user_id` = :user_id "
            . " LIMIT 20 "
            . ";",
            $this->settings->TABLES['LM_USER_VIEWED_LISTINGS']
        ), [
            'user_id' => $lead->info('id')
        ]);

        $listings = $this->getListingData($results);

        return $listings;
    }

    /**
     * @param array $results
     * @param boolean $limit
     * @throws UnexpectedValueException If query error occurs
     * @return array
     */
    protected function getListingData($results, $expired = false)
    {
        $listings = [];
        $timestamps = [];
        $feeds = [];
        foreach($results as $result){
            $feeds[$result['idx']][] = $result['ListingMLS'];
            $timestamps[$result['ListingMLS']] = $result['timestamp'];
            if($expired) {
                $preListings[$result['idx']][$result['ListingMLS']] = $result;
            }
        }
        foreach($feeds as $feed => $mlsNumbers) {
            $results = $this->listingStore->getListings($feed, $mlsNumbers, self::SELECT_COLUMNS);
            foreach($results as $result) {
                $result['timestamp'] = $timestamps[$result['ListingMLS']];
                $result['url'] = ($feed === 'cms' ? '/listing/cms/' . $result['ListingMLS'] : $result['url_details']);
                $listings[] = $result;
            }
            if($expired) {
                if (is_array($preListings[$feed])) {
                    foreach ($preListings[$feed] as $mls => $pre) {
                        if (!isset($results[$mls])) {
                            $pre['Address'] = '[Deleted Listing]';
                            $listings[] = $pre;
                        }
                    }
                }
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
        if ($type == 'favorites') $response =  $this->getListingFavorites($lead);
        if ($type == 'recommended') $response =  $this->getListingRecommendations($lead);
        if ($type == 'views') $response =  $this->getListingViews($lead);

        return $response ?: [];
    }
}
