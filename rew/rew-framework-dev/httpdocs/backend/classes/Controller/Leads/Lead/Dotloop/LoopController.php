<?php

namespace REW\Backend\Controller\Leads\Lead\Dotloop;

use REW\Backend\Auth\Leads\LeadAuth;
use REW\Backend\Auth\PartnersAuth;
use REW\Backend\Controller\AbstractController;
use REW\Backend\Exceptions\MissingId\MissingLeadException;
use REW\Backend\Exceptions\UnauthorizedPageException;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\CacheInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Core\Interfaces\SettingsInterface;
use \Backend_Agent;
use \Backend_Lead;
use \Partner_DotLoop;
use \PDOException;
use \Settings;

/**
 * DotLoopController
 * @package REW\Backend\Controller\Leads\Lead\Dotloop
 */
class LoopController extends AbstractController
{

    /**
     * @var FactoryInterface
     */
    protected $view;

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var Partner_DotLoop
     */
    protected $dotloop_api;

    /**
     * @var FormatInterface
     */
    protected $format;

    /**
     * @var LogInterface
     */
    protected $log;

    /**
     * @var NoticesCollectionInterface
     */
    protected $notices;

    /**
     * @var Settingsinterface
     */
    protected $settings;

    /**
     * @param FactoryInterface $view
     * @param AuthInterface $auth
     * @param CacheInterface $cache
     * @param DBInterface $db
     * @param FormatInterface $format
     * @param LogInterface $log
     * @param NoticesCollectionInterface $notices
     * @param SettingsInterface $settings
     */
    public function __construct(
        FactoryInterface $view,
        AuthInterface $auth,
        CacheInterface $cache,
        DBInterface $db,
        FormatInterface $format,
        LogInterface $log,
        NoticesCollectionInterface $notices,
        SettingsInterface $settings
    ) {
        $this->view = $view;
        $this->auth = $auth;
        $this->cache = $cache;
        $this->db = $db;
        $this->format = $format;
        $this->log = $log;
        $this->notices = $notices;
        $this->settings = $settings;
    }

    /**
     * @throws MissingLeadException If lead not found
     * @throws UnauthorizedPageException If permissions are invalid
     */
    public function __invoke()
    {

        if (null === ($lead = $this->getLead())) {
            throw new MissingLeadException();
        }

        // Get Lead Authorization
        $lead = new Backend_Lead($lead);
        $leadAuth = new LeadAuth($this->settings, $this->auth, $lead);

        // Not authorized to view all leads
        if (!$leadAuth->canViewLead()) {
            throw new UnauthorizedPageException(
                'You do not have permission to view this lead'
            );
        }

        // DotLoop API Handler
        $this->dotloop_api = new Partner_DotLoop(Backend_Agent::load($this->auth->info('id')), $this->db);

        // Check Partner Auth + Access Token Validation
        $this->checkAccess($lead);

        // Check Contact's DotLoop ID
        $dotloop_contact_id = $this->getLeadDotLoopID($lead);
        if (empty($dotloop_contact_id)) {
            $this->notices->error('Failed to load loop details - lead has not been connected to DotLoop.');
            // Redirect to lead connect page
            header(sprintf('Location: %sleads/lead/dotloop/?id=%s', $this->settings->URLS['URL_BACKEND'], $lead->getId()));
            exit;
        }

        // Get Loop Info From Local DB Record
        $loop = $this->loadLoop($lead);
        if (empty($loop)) {
            $this->notices->error('Failed to retreive loop details.');
            // Redirect to loop manager page
            header(sprintf('Location: %sleads/lead/dotloop/?id=%s', $this->settings->URLS['URL_BACKEND'], $lead->getId()));
            exit;
        }

        // Render lead summary header (menu/title/preview)
        echo $this->view->render('inc/tpl/partials/lead/summary.tpl.php', [
            'title' => 'DotLoop - Loop Details',
            'lead' => $lead,
            'leadAuth' => $leadAuth,
            'back' => $back,
        ]);

        // Render Lead Connect template file
        echo $this->view->render('::pages/leads/lead/dotloop/loop', [
            'loop' => $loop,
            'format' => $this->format,
            'rate_limit_info' => $this->dotloop_api->getRateLimitStatus(),
        ]);

        // Output any errors that occurred
        if (!empty($this->dotloop_api->getLastError())) {
            $this->notices->error($this->dotloop_api->getLastError());
        }
    }

    /**
     * Load the Lead from the ID in the URL
     *
     * @return mixed (array|null)
     */
    protected function getLead()
    {
        $lead_id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

        // Make Sure Lead Exists With Provided ID
        try {
            $lead = $this->db->fetch(sprintf("SELECT * FROM `%s` WHERE `id` = :id;", LM_TABLE_LEADS), ['id' => $lead_id]);
        } catch (PDOException $e) {
            $this->log->error($e->getMessage());
        }
        return $lead ?: null;
    }

    /**
     * Check User's Permissions + API Access Status
     *
     * @param Backend_lead $lead
     * @throws UnauthorizedPageException
     * @return void
     */
    protected function checkAccess($lead)
    {
        $partnersAuth = new PartnersAuth($this->settings);
        // Insufficient Permissions
        if (!$partnersAuth->canManageDotloop($this->auth)) {
            throw new UnauthorizedPageException();
            // Invalid Integration Status
        } else if (!$this->dotloop_api->validateAPIAccess()) {
            // Only redirect if the error was not caused by the rate limit
            if ($this->dotloop_api->getLastAPIErrorID() !== Partner_DotLoop::API_ERRORS['RATE_LIMIT_EXCEEDED']) {
                $this->notices->warning($this->dotloop_api->getLastError());
                header(sprintf('Location: %sleads/lead/summary/?id=%s', $this->settings->URLS['URL_BACKEND'], $lead->getId()));
                exit;
            }
        }
    }

    /**
     * Get Lead's DotLoop ID if Available
     *
     * @param Backend_Lead $lead
     * @return mixed (int|null)
     */
    protected function getLeadDotLoopID(Backend_Lead $lead)
    {
        // Check for Lead's DotLoop Contact ID
        $dotloop_contact_data = $this->dotloop_api->getLeadConnectData($lead->getId());
        return (!empty($dotloop_contact_data)) ? intval($dotloop_contact_data['dotloop_contact_id']) : null;
    }

    /**
     * Load Loop Data from Local DB + API Calls
     *
     * @param Backend_Lead $lead
     * @return array
     */
    protected function loadLoop(Backend_Lead $lead)
    {
        $loop = [];
        $basic_info = $this->dotloop_api->getLocalLoopInfo($this->auth->info('partners.dotloop.account_id'), $_GET['loop'], $lead->info('email'));
        if (!empty($basic_info)) {
            // Basic Loop Info Loaded from Local DB Record
            $loop['basic_info'] = $basic_info;

            // Get Participants from Local DB Records
            $loop['participants'] = $this->dotloop_api->getLocalLoopParticipants($this->auth->info('partners.dotloop.account_id'), $_GET['loop']);

            // Request + Cache the Advanced Loop Details
            $index = hash('sha256', $this->auth->info('partners.dotloop.account_id') . $loop['basic_info']['dotloop_profile_id'] . $loop['basic_info']['dotloop_loop_id'] . 'loop_details');
            $cached = $this->cache->getCache($index);
            if (!is_null($cached)) {
                $loop['details'] = $cached;
            } else {
                $loop['details'] = $this->dotloop_api->getLoopDetails($loop['basic_info']['dotloop_profile_id'], $loop['basic_info']['dotloop_loop_id']);
                // Cache for 15 mins. Just want to avoid multiple API calls for page refreshes, etc...
                if (!empty($loop['details'])) {
                    $this->cache->setCache($index, $loop['details'], false, (60 * 15));
                }
            }
            unset($index, $cached);

            /**
             * @TODO - Task lists aren't worth displaying until we can expand on them (list tasks, task details)
             * First will need to store task data locally - otherwise it will be too taxing on our API rate limit
             */
//             $loop['task_lists'] = $dotloopApi->getLoopTaskLists($loop['basic_info']['dotloop_profile_id'], $loop['basic_info']['dotloop_loop_id']);

            // Request + Cache the Loop Activity History
            $index = hash('sha256', $this->auth->info('partners.dotloop.account_id') . $loop['basic_info']['dotloop_profile_id'] . $loop['basic_info']['dotloop_loop_id'] . 'loop_activities');
            $cached = $this->cache->getCache($index);
            if (!is_null($cached)) {
                $loop['activities'] = $cached;
            } else {
                $loop['activities'] = $this->dotloop_api->getLoopActivities($loop['basic_info']['dotloop_profile_id'], $loop['basic_info']['dotloop_loop_id']);
                // Cache for 15 mins. Just want to avoid multiple API calls for page refreshes, etc...
                if (!empty($loop['activities'])) {
                    $this->cache->setCache($index, $loop['activities'], false, (60 * 15));
                }
            }
            unset($index, $cached);
        }
        return $loop ?: [];
    }
}
