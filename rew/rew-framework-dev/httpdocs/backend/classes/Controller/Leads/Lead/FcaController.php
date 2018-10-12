<?php

namespace REW\Backend\Controller\Leads\Lead;

use REW\Backend\Auth\LeadsAuth;
use REW\Backend\Exceptions\UnauthorizedPageException;
use REW\Backend\Exceptions\MissingId\MissingLeadException;
use REW\Backend\Exceptions\PageNotFoundException;
use REW\Backend\Auth\PartnersAuth;
use REW\Backend\Partner\Firstcallagent as Partner_Firstcallagent;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Backend\Auth\Leads\LeadAuth;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\EnvironmentInterface;
use \Backend_Lead;

/**
 * FcaController
 * @package REW\Backend\Controller\Leads\Lead
 */
class FcaController extends AbstractLeadController
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
     * @var PartnersAuth
     */
    protected $partnersAuth;

    /**
     * @var PartnersAuth
     */
    protected $leadAuth;

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var Partner_Firstcallagent
     */
    protected $firstcallagent;

    /**
     * @var NoticesCollectionInterface
     */
    protected $notices;

    /**
     * @var EnvironmentInterface
     */
    protected $env_settings;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @param FactoryInterface $view
     * @param AuthInterface $auth
     * @param DBInterface $db
     * @param NoticesCollectionInterface $notices
     * @param EnvironmentInterface $env_settings
     * @param SettingsInterface $settings
     * @param Partner_Firstcallagent $firstCallAgent
     */
    public function __construct(
        FactoryInterface $view,
        AuthInterface $auth,
        PartnersAuth $partnersAuth,
        DBInterface $db,
        NoticesCollectionInterface $notices,
        EnvironmentInterface $env_settings,
        SettingsInterface $settings,
        Partner_Firstcallagent $firstcallagent
    ) {
        $this->view = $view;
        $this->auth = $auth;
        $this->partnersAuth = $partnersAuth;
        $this->db = $db;
        $this->notices = $notices;
        $this->env_settings = $env_settings;
        $this->settings = $settings;
        $this->firstcallagent = $firstcallagent;

        // Get Valid Lead
        $lead = $this->getLeadFromRequest();
        $this->leadAuth = new LeadAuth($this->settings, $this->auth, $lead);
    }

    /**
     * @throws UnauthorizedPageException If Auth not found to manage
     * @throws PageNotFoundException
     */
    public function __invoke()
    {

        // Check that the module is enabled
        if (!$this->firstcallagent->isEnabled()) {
            throw new PageNotFoundException();
        }

        // Authorized to manage FCA settings
        if (!$this->leadAuth->canManageFirstcallagent()) {
            throw new UnauthorizedPageException('You do not have permission to view First Call Agent Partner.');
        }

        $settings = $this->firstcallagent->getSettings();

        if (empty($settings)) {
            throw new UnauthorizedPageException('You do not have First Call Agent set up.');
        }

        // Get Valid Lead
        $lead = $this->getLeadFromRequest();

        // Check for Missing Lead Id
        if (empty($lead->getRow())) {
            throw new MissingLeadException();
        }

        if ($this->firstcallagent->agentExcluded($lead->info('agent'))) {
            throw new UnauthorizedPageException('This Lead\'s Agent is Excluded from First Call Agent.');
        }

        // Check Lead Authorization
        if (!$this->leadAuth->canViewMessages()) {
            throw new UnauthorizedPageException('You do not have permission to view lead First Call Agent.');
        }

        $leadSettings = $this->firstcallagent->getLeadSettings($lead);

        // Lead has been sent to FCA
        if ($leadSettings === false) {
            $leadSettings = ['sent' => 'false', 'can_send' => $this->env_settings->isREW()];
        } else {
            $leadSettings['can_send'] = $this->env_settings->isREW();
        }

        // Manually send lead to FCA
        if (isset($_POST['send']) && $_SERVER['REQUEST_METHOD'] === 'POST' && $leadSettings['can_send'] === true) {
            $sendlead = $this->firstcallagent->sendLead($lead, true);

            if ($sendlead) {
                $this->notices->success('Lead sent to FCA');

                header('Location: ' . URL_BACKEND . 'leads/lead/fca/?id=' . $_POST['lead']);
                exit;
            } else {
                $this->notices->error('There was an issue sending lead to FCA: ' . $this->firstcallagent->getLastError());
            }
        }

        // Flag the lead to stop FCA from calling the lead
        if (isset($_POST['no_call']) && $_SERVER['REQUEST_METHOD'] === 'POST' && $leadSettings['sent'] == 'true' && $leadSettings['no_call'] == 'false') {
            $sendlead = $this->firstcallagent->closeLead($lead);

            if ($sendlead) {
                $this->notices->success('Lead Set to No Call on FCA');

                header('Location: ' . URL_BACKEND . 'leads/lead/fca/?id=' . $_POST['lead']);
                exit;
            } else {
                $this->notices->error('There was an issue updating lead to No Call on FCA');
            }
        }

        // Render lead summary header (menu/title/preview)
        $summaryMenu = $this->view->render('::partials/lead/summary', [
            'title' => 'First Call Agent',
            'lead' => $lead,
            'leadAuth' => $this->leadAuth
        ]);

        // Render template file
        echo $this->view->render('::pages/leads/lead/fca', [
            'leadSettings' => $leadSettings,
            'summaryMenu' => $summaryMenu,
            'lead' => $lead->getRow()
        ]);
    }
}
