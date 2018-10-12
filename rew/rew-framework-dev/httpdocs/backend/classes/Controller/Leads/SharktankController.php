<?php

namespace REW\Backend\Controller\Leads;

use REW\Backend\Auth\LeadsAuth;
use REW\Backend\Controller\AbstractController;
use REW\Backend\Exceptions\UnauthorizedPageException;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Backend\Auth\Leads\LeadAuth;
use \Backend_Agent;
use \Backend_Lead;
use \Exception;
use \History_Event_Update_Assign;
use \History_User_Lead;
use \History_User_Agent;

/**
 * SharkTankController
 * @package REW\Backend\Controller\Leads
 */
class SharktankController extends AbstractController
{

    /**
     * Page Limit
     * @var int TANK_PAGE_LIMIT
     */
    const TANK_PAGE_LIMIT = 10;

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var DBInterface
     */
    protected $db;

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
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var FactoryInterface
     */
    protected $view;

    /**
     * @var boolean
     */
    protected $isSharktankEnabled;

    /**
     * @param AuthInterface $auth
     * @param DBInterface $db
     * @param FormatInterface $format
     * @param LogInterface $log
     * @param NoticesCollectionInterface $notices
     * @param SettingsInterface $settings
     * @param FactoryInterface $view
     */
    public function __construct(
        AuthInterface $auth,
        DBInterface $db,
        FormatInterface $format,
        LogInterface $log,
        NoticesCollectionInterface $notices,
        SettingsInterface $settings,
        FactoryInterface $view
    ) {
        $this->auth = $auth;
        $this->db = $db;
        $this->format = $format;
        $this->log = $log;
        $this->notices = $notices;
        $this->settings = $settings;
        $this->view = $view;
    }

    /**
     * return @void
     */
    public function __invoke()
    {
        $leadsAuth = new LeadsAuth($this->settings);

        $this->isSharktankEnabled = $this->isSharktankEnabled();

        // Check appropriate permissions for Shark Tank page
        $this->checkAccess($leadsAuth);

        // Check if Single-Lead View is Being Requested
        $single_lead = (!empty($_GET['tank_lead'])) ? intval($_GET['tank_lead']) : null;

        // Handle lead claim attempt
        if (!empty($_GET['claim'])) {
            if ($this->claimLeadHandler(intval($_GET['claim']))) {
                if (!empty($single_lead) && $_GET['tank_lead'] === $_GET['claim']) {
                    header(sprintf('Location: %sleads/lead/summary/?id=%s', $this->settings->URLS['URL_BACKEND'], $_GET['claim']));
                } else {
                    header(sprintf('Location: %sleads/sharktank/?success', $this->settings->URLS['URL_BACKEND']));
                }
                exit;
            }
        }

        // Count Total Shark Tank Leads
        $tank_leads_total = $this->countSharkTankLeads($single_lead);

        // Get Shark Tank Leads
        $tank_leads = ($tank_leads_total > 0) ? $this->getSharkTankLeads($single_lead) : [];

        // Redirect to summary page if you are viewing a lead you've previously claimed
        if (empty($tank_leads) && !empty($single_lead)) {
            $this_lead = Backend_Lead::load($single_lead);
            if ($this_lead->info('agent') == $this->auth->info('id')) {
                header(sprintf('Location: %sleads/lead/summary/?id=%s', $this->settings->URLS['URL_BACKEND'], $this_lead->getId()));
            }
        }

        if ($tank_leads_total > self::TANK_PAGE_LIMIT) {
            $pagination = generate_pagination($tank_leads_total, $_GET['p'], self::TANK_PAGE_LIMIT);
        }

        array_walk($tank_leads, function(&$lead) {
            $leadAuth = new LeadAuth($this->settings, $this->auth, $lead);
            $lead['authCanViewLead'] = $leadAuth->canViewLead();
        });

        echo $this->view->render('::/pages/leads/sharktank', [
            'authuser' => $this->auth,
            'format' => $this->format,
            'leadsAuth' => $leadsAuth,
            'isSharktankEnabled' => $this->isSharktankEnabled,
            'pagination' => $pagination,
            'tank_leads' => $tank_leads,
            'view' => $this->view
        ]);

        echo $this->view->render('::partials/pagination.tpl.php', [
            'links' => $pagination['links'],
            'prev' => $pagination['prev'],
            'next' => $pagination['next']
        ]);
    }

    /**
     * Handle Lead Claim Attempts
     *
     * @param int $claim_lead_id
     * @return bool
     */
    protected function claimLeadHandler($claim_lead_id)
    {
        if ($claim_lead_id > 0) {
            $claim = $this->getSharkTankLeads($claim_lead_id)[0];
            if (!empty($claim)) {
                // Transaction Start
                $this->db->beginTransaction();
                try {
                    // Assign lead and update their status
                    $update_lead = $this->db->prepare(sprintf(
                        "UPDATE `%s` SET "
                        . " `in_shark_tank` = 'false', "
                        . " `timestamp_out_shark_tank` = NOW() "
                        . " WHERE `id` = :user_id "
                        . ";",
                        $this->settings->TABLES['LM_LEADS']
                    ));
                    if ($update_lead->execute(['user_id' => $claim->getId()])) {
                        $shark = Backend_Agent::load($this->auth->info('id'));
                        $claim->status('accepted', $this->auth, false);
                        $claim->assign($shark, $this->auth, false);

                        // Log Event: Shark Claim
                        $event = new History_Event_Update_Assign(array(
                            'agent_id' => $shark->getId(),
                            'claimed' => true
                        ), array(
                            new History_User_Lead($claim->getId()),
                            new History_User_Agent($shark->getId()),
                            (!empty($this->auth) && $this->auth->info('id') != $shark->getId() ? $this->auth->getHistoryUser() : null)
                        ));

                        // Save to DB
                        $event->save($this->db);
                    }
                    // Transaction Commit
                    if ($this->db->commit()) {
                        // Success
                        $this->notices->success(sprintf('Successfully claimed lead from the Shark Tank: %s %s', $claim->info('first_name'), $claim->info('last_name')));
                        return true;
                    }
                } catch (Exception $e) {
                    $this->notices->error('Failed to claim lead.');
                    $this->log->Error($e->getMessage());
                    // Transaction Rollback
                    $this->db->rollback();
                }
            } else {
                $this->notices->error('Failed to claim lead. It is possible that the lead has already been claimed.');
            }
        } else {
            $this->notices->error('Invalid lead claim request.');
        }
        return false;
    }

    /**
     * Check Access to Shark Tank
     *
     * @var LeadsAuth $leadsAuth
     * @throws UnauthorizedPageException
     */
    protected function checkAccess(LeadsAuth $leadsAuth)
    {
        // Make sure user is an Agent
        if (!$leadsAuth->canManageOwn($this->auth)) {
            throw new UnauthorizedPageException(
                'You do not have permission to view this page'
            );
        }
        // Check if Agent can Access Shark Tank
        if (!$leadsAuth->canAccessSharkTank($this->auth) || !$this->isSharktankEnabled) {
            header(sprintf('Location: %sleads/', $this->settings->URLS['URL_BACKEND']));
            exit;
        }
    }

    /**
     * Is Sharktank Enabled?
     *
     * @return boolean
     */
    public function isSharktankEnabled()
    {
        $query = $this->db->prepare(sprintf(
            "SELECT `shark_tank` FROM `%s` WHERE `agent` = :agent;",
            $this->settings->TABLES['DEFAULT_INFO']
        ));
        $params = ['agent' => 1];
        if ($query->execute($params)) {
            $checkSetting = $query->fetchColumn();
        } else {
            $this->notices->error('Could not look up Shark Tank setting.');
        }
        return $checkSetting === 'true' ? true : false;
    }

    /**
     * Get Total Count of Shark Tank Leads
     *
     * @param int $lead_id (optional)
     * @return int
     */
    protected function countSharkTankLeads($lead_id = null)
    {
        $total = null;
        try {
            $query = $this->db->prepare(sprintf(
                "SELECT COUNT(`id`) AS `total` "
                . " FROM `%s` "
                . " WHERE `agent` = 1 "
                . " AND `status` = 'unassigned' "
                . " AND `in_shark_tank` = 'true' "
                . (!empty($lead_id)
                    ? " AND `id` = :lead_id "
                    : ""
                )
                . ";",
                $this->settings->TABLES['LM_LEADS']
            ));
            $params = [];
            if (!empty($lead_id)) {
                $params['lead_id'] = $lead_id;
            }
            if ($query->execute($params)) {
                $total = $query->fetch();
                $total = $total['total'];
            } else {
                $this->notices->error('Failed to load Shark Tank leads, please try again.');
            }
        } catch (Exception $e) {
            $this->log->Error($e->getMessage());
        }
        return intval($total);
    }

    /**
     * Get All Shark Tank Leads, or One Specific Shark Tank Lead
     *
     * @param int $lead_id (optional)
     * @return array
     */
    protected function getSharkTankLeads($lead_id = null)
    {
        $tank_leads = [];
        // Query Pagination Offset
        $cur_page = intval($_GET['p']);
        $offset = ($cur_page > 1) ? ((($cur_page - 1) * self::TANK_PAGE_LIMIT)) : 0;
        try {
            $query = $this->db->prepare(sprintf(
                "SELECT `id`, `image`, `first_name`, `last_name`, `timestamp_in_shark_tank`, `value` "
                . " FROM `%s` "
                . " WHERE `agent` = 1 "
                . " AND `status` = 'unassigned' "
                . " AND `in_shark_tank` = 'true' "
                . (!empty($lead_id)
                    ? " AND `id` = :lead_id "
                    : ""
                )
                . " ORDER BY `timestamp_in_shark_tank` DESC "
                . " LIMIT %s,%s "
                . ";",
                $this->settings->TABLES['LM_LEADS'],
                $offset,
                self::TANK_PAGE_LIMIT
            ));
            $params = [];
            if (!empty($lead_id)) {
                $params['lead_id'] = $lead_id;
            }
            if ($query->execute($params)) {
                while ($lead = $query->fetch()) {
                    $tank_leads[] = Backend_Lead::load($lead['id']);
                }
            } else {
                $this->notices->error('Failed to load Shark Tank leads, please try again.');
            }
        } catch (Exception $e) {
            $this->log->Error($e->getMessage());
        }
        return $tank_leads;
    }
}
