<?php

namespace REW\Api\Internal\Controller\Route\Crm\Leads\Lead\Listing;

use REW\Api\Internal\Exception\BadRequestException;
use REW\Api\Internal\Exception\NotFoundException;
use REW\Api\Internal\Exception\ServerSuccessException;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\FormatInterface;
use Slim\Http\Response;
use Slim\Http\Request;
use \Backend_Agent;
use \Backend_Agent_Notifications;
use \Backend_Lead;
use \Backend_Mailer_ListingRecommendation;
use \History_Event_Action_SavedListing;
use \History_User_Lead;
use \Lang;
use \Locale;
use \Util_IDX;

/**
 * Lead Listing Recommend Controller
 * @package REW\Api\Internal\Controller\Route\Crm\Leads\Lead\Listing
 */
class Recommend implements ControllerInterface
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
     * @var DBInterface
     */
    protected $dbIdx;

    /**
     * @var IDX
     */
    protected $idx;

    /**
     * @var IDXFactoryInterface
     */
    protected $idxFactory;

    /**
     * @var Backend_Lead
     */
    protected $lead;

    /**
     * @var array
     */
    protected $listing;

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
     * @var FormatInterface
     */
    protected $format;

    /**
     * @param AuthInterface $auth
     * @param DBInterface $db
     * @param IDXFactoryInterface $idxFactory
     * @param SettingsInterface $settings
     * @param FormatInterface $format
     */
    public function __construct(
        AuthInterface $auth,
        DBInterface $db,
        IDXFactoryInterface $idxFactory,
        SettingsInterface $settings,
        FormatInterface $format
    ) {
        $this->auth = $auth;
        $this->db = $db;
        $this->idxFactory = $idxFactory;
        $this->settings = $settings;
        $this->format = $format;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $routeParams
     * @return array
     * @throws ServerSuccessException
     */
    public function __invoke(Request $request, Response $response, $routeParams = [])
    {
        $body = json_decode($request->getBody());
        $this->post = (!empty($body) ? (array) $body : []);
        $this->routeParams = $routeParams;

        $this->post['mls_number'] = trim($this->post['mls_number']);

        $this->lead = $this->fetchLead();

        $this->loadIDXObjects();

        $this->checkRequestValidity();

        $this->listing = $this->fetchIDXListing();

        $this->recommendListing();

        throw new ServerSuccessException('The listing has been recommended successfully.');
    }

    /**
     * Check the validity of the API request
     * @return void
     * @throws BadRequestException
     */
    protected function checkRequestValidity()
    {
        if (empty($this->post['mls_number'])) {
            throw new BadRequestException(sprintf('%s Number is a required request field.', Lang::write('MLS')));
        }

        // Check Duplicate
        if (!empty($this->post['mls_number'])) {
            $checkDuplicate = $this->db->fetch(sprintf(
                "SELECT "
                . " `id`, "
                . " `agent_id`, "
                . " `associate` "
                . " FROM %s "
                . " WHERE `user_id` = :lead_id "
                . " AND `mls_number` = :mls_number "
                . ";",
                $this->settings->TABLES['LM_USER_LISTINGS']
            ), [
                'lead_id' => $this->routeParams['leadId'],
                'mls_number' => $this->post['mls_number']
            ]);
            if (!empty($checkDuplicate)) {
                if (!empty($checkDuplicate['agent_id']) || !empty($checkDuplicate['associate'])) {
                    throw new BadRequestException('This listing has already been recommended to this lead.');
                } else {
                    throw new BadRequestException(sprintf('This listing is already a %s listing to this lead.', Locale::spell('favorite')));
                }
            }
        }
    }

    /**
     * @return array
     * @throws BadRequestException
     */
    protected function fetchIDXListing()
    {
        $searchWhere = sprintf(
            " %s = '%s' ",
            $this->idx->field('ListingMLS'),
            $this->dbIdx->cleanInput($this->post['mls_number'])
        );

        // Any global criteria
        $this->idx->executeSearchWhereCallback($searchWhere);

        // Locate Listing
        $listing = $this->dbIdx->fetchQuery(sprintf(
            "SELECT "
            . " %s "
            . " FROM %s "
            . " WHERE %s "
            . " LIMIT 1 "
            . ";",
            $this->idx->selectColumns(),
            $this->idx->getTable(),
            $searchWhere
        ));

        // Unknown Listing
        if (empty($listing)) {
            throw new BadRequestException(sprintf('Listing Not Found: %s%s', Lang::write('MLS_NUMBER'), $this->post['mls_number']));
        }

        return Util_IDX::parseListing($this->idx, $this->dbIdx, $listing);
    }

    /**
     * @throws NotFoundException
     * @return array
     */
    protected function fetchLead()
    {
        // Check if Lead Exists
        $lead = $this->db->fetch(sprintf(
            "SELECT * FROM `%s` WHERE `id` = :id;",
            $this->settings->TABLES['LM_LEADS']
        ), ['id' => $this->routeParams['leadId']]);

        if (empty($lead)) {
            throw new NotFoundException('Failed to find a lead with the requested ID.');
        }

        // Load the Lead Object
        return new Backend_Lead($lead);
    }

    /**
     * @return void
     */
    protected function loadIDXObjects()
    {
        $feed = !empty($this->post['feed']) ? $this->post['feed'] : '';
        $this->idx = $this->idxFactory->getIdx($feed);
        $this->dbIdx = $this->idxFactory->getDatabase($feed);
    }

    /**
     * @return void
     */
    protected function replaceTags()
    {
        $verify = sprintf($this->settings['SETTINGS']['URL_IDX_VERIFY'], $this->format->toGuid($this->lead->info('guid')));

        // Replace Tags with lead and agent info
        $tags = [
            '{first_name}'=> $this->lead->info('first_name'),
            '{last_name}'=> $this->lead->info('last_name'),
            '{email}'=> $this->lead->info('email'),
            '{verify}'=> $verify,
            '{signature}'=> $this->auth->info('signature')
        ];
        foreach($tags as $tag => $val ) {
            $this->post['message'] = str_replace($tag, $val, $this->post['message']);
        }
    }

    /**
     * @throws NotFoundException
     * @return void
     */
    protected function recommendListing()
    {
        $query = $this->db->prepare(sprintf(
            "INSERT INTO %s SET "
            . "`user_id`     = :lead_id, "
            . ($this->auth->isAgent()
                ? "`agent_id` = :auth_id, "
                : "`agent_id` = NULL, "
            )
            . ($this->auth->isAssociate()
                ? "`associate` = :auth_id, "
                : "`associate` = NULL,"
            )
            . "`mls_number`  = :mls_number, "
            . "`table`       = :table, "
            . "`idx`         = :idx, "
            . "`type`        = :type, "
            . "`city`        = :city, "
            . "`subdivision` = :subdivision, "
            . "`bedrooms`    = :bedrooms, "
            . "`bathrooms`   = :bathrooms, "
            . "`sqft`        = :sqft, "
            . "`price`       = :price, "
            . "`timestamp`   = NOW()"
            . ";",
            $this->settings->TABLES['LM_USER_LISTINGS']
        ));
        $params = [
            'auth_id'     => $this->auth->info('auth'),
            'bathrooms'   => ($this->listing['NumberOfBathrooms'] ?: 0),
            'bedrooms'    => ($this->listing['NumberOfBedrooms'] ?: 0),
            'city'        => ($this->listing['AddressCity'] ?: ''),
            'idx'         => $this->idx->getName(),
            'lead_id'     => $this->routeParams['leadId'],
            'mls_number'  => $this->listing['ListingMLS'],
            'price'       => ($this->listing['ListingPrice'] ?: 0),
            'sqft'        => ($this->listing['NumberOfSqFt'] ?: 0),
            'subdivision' => ($this->listing['AddressSubdivision'] ?: ''),
            'table'       => $this->idx->getTable(),
            'type'        => ($this->listing['ListingType'] ?: '')
        ];
        if ($query->execute($params)) {
            // Log Event: Agent Recommended Listing
            $event = new History_Event_Action_SavedListing([
                'listing' => $this->listing
            ], [
                new History_User_Lead($this->routeParams['leadId']),
                $this->auth->getHistoryUser()
            ]);

            $event->save();

            $this->replaceTags();

            if (!empty($this->post['notify'])) {
                $mailer = new Backend_Mailer_ListingRecommendation([
                    'listing' => $this->listing,
                    'message' => $this->post['message'],                   // Email Message
                    'signature' => $this->auth->info('signature'),        // Signature
                    'append' => ($this->auth->info('add_sig') == 'Y')  // Append Signature
                ]);
                $agent = new Backend_Agent($this->auth->getInfo());
                $mailer = $agent->checkOutgoingNotifications($mailer, Backend_Agent_Notifications::OUTGOING_LISTING_RECOMMEND);
                $mailer->setSender($this->auth->info('email'), $this->auth->getName());
                $mailer->setRecipient($this->lead->info('email'), $this->lead->getName());
                if (!$mailer->Send()) {
                    throw new NotFoundException('The listing recommendation was saved, but there was an error sending the lead notification.');
                }
            }
        } else {
            throw new NotFoundException('Failed to store listing recommendation');
        }
    }
}