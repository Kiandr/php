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
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Core\Interfaces\SettingsInterface;
use \Backend_Agent;
use \Backend_Lead;
use \Partner_DotLoop;
use \Settings;

/**
 * UnlinkController
 * @package REW\Backend\Controller\Leads\Lead\DotLoop
 */
class UnlinkController extends AbstractController
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
     * @param DBInterface $db
     * @param FormatInterface $format
     * @param LogInterface $log
     * @param NoticesCollectionInterface $notices
     * @param SettingsInterface $settings
     */
    public function __construct(
        FactoryInterface $view,
        AuthInterface $auth,
        DBInterface $db,
        FormatInterface $format,
        LogInterface $log,
        NoticesCollectionInterface $notices,
        SettingsInterface $settings
    ) {
        $this->view = $view;
        $this->auth = $auth;
        $this->db = $db;
        $this->format = $format;
        $this->log = $log;
        $this->notices = $notices;
        $this->settings = $settings;
        $this->partners_auth = new PartnersAuth($this->settings);
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

        // Load Agent Object
        $agent = Backend_Agent::load($this->auth->info('id'));

        // DotLoop API Handler
        $this->dotloop_api = new Partner_DotLoop($agent, $this->db);

        // Check Partner Auth + Access Token Validation
        $this->checkAccess($lead);

        // Handle Lead Connection Submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST'
            && null !== ($connect_redirect = $this->formSubmitHandler($lead, $agent))
        ) {
            header($connect_redirect);
            exit;
        }

        // Render lead summary header (menu/title/preview)
        echo $this->view->render('inc/tpl/partials/lead/summary.tpl.php', [
            'title' => 'DotLoop - Unlink Lead',
            'lead' => $lead,
            'leadAuth' => $leadAuth
        ]);

        // Render Lead Unlink template file
        echo $this->view->render('::pages/leads/lead/dotloop/unlink', [
            'lead' => $lead
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
        // Insufficient Permissions
        if (!$this->partners_auth->canManageDotloop($this->auth)) {
            throw new UnauthorizedPageException();
        }
    }

    /**
     * Handle Form Submissions
     *
     * @param Backend_Lead $lead
     * @param Backend_Agent $agent
     * @return mixed (string|null)
     */
    protected function formSubmitHandler(Backend_Lead $lead, Backend_Agent $agent)
    {
        // Handle Lead Unlink Submission
        if (!empty($_POST['unlink_lead']) && $_GET['id'] === $_POST['unlink_lead']) {
            if ($agent_partners = json_decode($agent->info('partners'), true)) {
                try {
                    $query = $this->db->prepare(sprintf(
                        "DELETE FROM `%s` "
                        . " WHERE `user_id` = :user_id "
                        . " AND `dotloop_account_id` = :dotloop_account_id "
                        . " AND `dotloop_contact_id` = :dotloop_contact_id "
                        . " LIMIT 1"
                        . ";",
                        Partner_Dotloop::TABLE_CONNECTED_LEADS
                    ));
                    if ($query->execute([
                        'user_id' => $lead->getId(),
                        'dotloop_account_id' => $agent_partners['dotloop']['account_id'],
                        'dotloop_contact_id' => $this->getLeadDotLoopID($lead),
                    ])) {
                        $this->notices->success(sprintf(
                            '%s has been successfully unlinked from DotLoop.',
                            $lead->info('first_name') . ' ' . $lead->info('last_name')
                        ));
                        return sprintf('Location: %sleads/lead/summary/?id=%s&success', $this->settings->getInstance()->URLS['URL_BACKEND'], $lead->getid());
                    }
                } catch (PDOException $e) {
                    $this->log->error($e->getMessage());
                    $this->notices->error(sprintf(
                        'Failed to unlink %s from DotLoop.',
                        $lead->info('first_name') . ' ' . $lead->info('last_name')
                    ));
                }
            } else {
                $this->notices->error('Failed to load DotLoop integration settings.');
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
}
