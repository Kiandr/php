<?php

namespace REW\Backend\Controller\Leads\Lead;

use REW\Backend\Auth\Leads\LeadAuth;
use REW\Backend\Auth\PartnersAuth;
use REW\Backend\Controller\AbstractController;
use REW\Backend\Exceptions\MissingId\MissingLeadException;
use REW\Backend\Exceptions\UnauthorizedPageException;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Core\Interfaces\SettingsInterface;
use \Backend_Agent;
use \Backend_Lead;
use \Log;
use \Partner_DotLoop;
use \Settings;

/**
 * DotLoopController
 * @package REW\Backend\Controller\Leads\Lead
 */
class DotloopController extends AbstractController
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
     * @param DBInterface $db
     * @param FormatInterface $format
     * @param NoticesCollectionInterface $notices
     * @param SettingsInterface $settings
     */
    public function __construct(
        FactoryInterface $view,
        AuthInterface $auth,
        DBInterface $db,
        FormatInterface $format,
        NoticesCollectionInterface $notices,
        SettingsInterface $settings
    ) {
        $this->view = $view;
        $this->auth = $auth;
        $this->db = $db;
        $this->format = $format;
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

        $agent = Backend_Agent::load($this->auth->info('id'));

        // DotLoop API Handler
        $this->dotloop_api = new Partner_DotLoop($agent, $this->db);

        // Check Partner Auth + Access Token Validation
        $this->checkAccess($lead);

        // Output any errors that occurred
        if (!empty($this->dotloop_api->getLastError())) {
            $this->notices->error($this->dotloop_api->getLastError());
        }

        // Handle Lead Connection Submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST'
            && null !== ($connect_redirect = $this->formSubmitHandler($lead))
        ) {
            header($connect_redirect);
            exit;
        }

        // Check Contact's DotLoop ID
        $dotloop_contact_id = $this->getLeadDotLoopID($lead);

        if ($partner_data = json_decode($agent->info('partners'), true)) {
            $assigned_loops = $this->dotloop_api->getLocalAssignedLoops($lead->info('email'), $partner_data['dotloop']['account_id']);
        } else {
            $assigned_loops = [];
        }

        /**
         * @TODO - Refactor connect/manage pages into separate controllers now that the TPL variable requirements have diverged
         */
        // Render Lead Connect template file
        echo $this->view->render($this->getRenderPath($dotloop_contact_id), [
            'authuser' => $this->auth,
            'format' => $this->format,
            'lead' => $lead,
            'leadAuth' => $leadAuth,
            'loop_deletion_statuses' => Partner_DotLoop::DELETION_STATUSES,
            'loop_participant_types' => Partner_DotLoop::PARTICIPANT_TYPES,
            'loop_statuses' => Partner_DotLoop::STATUSES,
            'loop_transaction_types' => Partner_DotLoop::TRANSACTION_TYPES,
            'view' => $this->view,
            'profiles' => $this->dotloop_api->getProfiles(),
            'profile_loops' => $this->dotloop_api->getProfilesLoops(),
            'profile_templates' => $this->dotloop_api->getProfilesTemplates(),
            'assigned_loops' => $assigned_loops,
            'rate_limit_info' => $this->dotloop_api->getRateLimitStatus(),
        ]);
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
            Log::error($e->getMessage());
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
     * Handle Form Submissions - Lead OR Loop Connections
     *
     * @param Backend_Lead $lead
     * @return mixed (string|null)
     */
    protected function formSubmitHandler(Backend_Lead $lead)
    {
        // Handle Lead Connect Submission
        if (!empty($_POST['connect_lead'])) {
            if (null !== ($dotloop_id = $this->dotloop_api->pushContact($_POST['connect_lead']))) {
                $this->notices->success('Successfully connected lead to DotLoop account.');
                return sprintf('Location: ?id=%s&success', $lead->getid());
            } else {
                $this->notices->error($this->dotloop_api->getLastError());
            }
        // Handle Loop Assignment Submissions
        } else if (!empty($_POST['loop_connect_type'])) {
            // New Loop
            if ($_POST['loop_connect_type'] === 'new') {
                // Create a New Loop and Attach the Lead to It
                if ($this->dotloop_api->pushLoopAndParticipant($_POST['profile_id'], $lead->getId(), $_POST['contact_type'], $_POST['loop_name'], $_POST['loop_transaction_type'], $_POST['loop_status'], $_POST['template_id'])) {
                    $this->notices->success('Successfully created new loop and assigned lead to it.');
                    return sprintf('Location: ?id=%s&success', $lead->getid());
                }
                // Existing Loop
            } else if ($_POST['loop_connect_type'] === 'existing') {
                list($profile_id, $loop_id) = explode(':', $_POST['profile_loop_ids']);
                // Push the lead into the existing loop
                if ($this->dotloop_api->pushLoopParticipant($profile_id, $lead->getId(), $_POST['contact_type'], $loop_id)) {
                    $this->notices->success('Successfully assigned lead to existing loop.');
                    return sprintf('Location: ?id=%s&success', $lead->getid());
                } else {
                    $this->notices->error($this->dotloop_api->getLastError());
                }
            }
        }
        return null;
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
     * Determine TPL Render Path Based on Lead's DotLoop Integration Status
     *
     * @var int $dotloop_contact_id
     * @return string
     */
    protected function getRenderPath($dotloop_contact_id)
    {
        // Determine target template file
        $render_path = '::pages/leads/lead/dotloop/connect';
        if (!empty($dotloop_contact_id)) {
            // Check if we get any response data using the lead's DotLoop ID
            $render_path = '::pages/leads/lead/dotloop/manage_loops';
        }
        return $render_path;
    }
}
