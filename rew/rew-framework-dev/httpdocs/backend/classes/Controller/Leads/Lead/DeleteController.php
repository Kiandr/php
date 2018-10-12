<?php

namespace REW\Backend\Controller\Leads\Lead;

use REW\Backend\Auth\Leads\LeadAuth;
use REW\Backend\Controller\AbstractController;
use REW\Backend\Exceptions\MissingId\MissingLeadException;
use REW\Backend\Exceptions\UnauthorizedPageException;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Core\Interfaces\SettingsInterface;
use \Backend_Lead;
use \PDOException;
use \Exception;

/**
 * DeleteController
 * @package REW\Backend\Controller\Leads\Lead
 */
class DeleteController extends AbstractController
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
     * @param FactoryInterface $view
     * @param AuthInterface $auth
     * @param DBInterface $db
     * @param LogInterface $log
     * @param NoticesCollectionInterface $notices
     * @param SettingsInterface $settings
     */
    public function __construct(
        FactoryInterface $view,
        AuthInterface $auth,
        DBInterface $db,
        LogInterface $log,
        NoticesCollectionInterface $notices,
        SettingsInterface $settings
    ) {
        $this->view = $view;
        $this->auth = $auth;
        $this->db = $db;
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

        // Query Lead
        $lead = $this->db->fetch(sprintf("SELECT * FROM `%s` WHERE `id` = :id;", LM_TABLE_LEADS), ['id' => $_GET['id']]);

        // Throw Missing Lead Exception
        if (empty($lead)) {
            throw new MissingLeadException();
        }

        // Create lead instance
        $lead = new Backend_Lead($lead);

        // Get Lead Authorization
        $leadAuth = new LeadAuth($this->settings, $this->auth, $lead);

        // Not authorized to delete this Lead
        if (!$leadAuth->canDeleteLead()) {
            throw new UnauthorizedPageException(
                'You do not have permission to delete this lead'
            );
        }

        // Confirm Delete
        if (!empty($_POST['delete'])) {
            try {
                $lead->delete($this->auth);
                $this->notices->success(sprintf('%s has successfully been deleted.', htmlspecialchars($lead->getNameOrEmail())));
            } catch (PDOException $e) {
                $this->notices->error(sprintf('Database operation failed. %s could not be deleted.', $lead->getNameOrEmail()));
                $this->log->error($e);
            } catch (Exception $e) {
                $this->notices->error(sprintf('%s could not be deleted.', $lead->getNameOrEmail()));
                $this->log->error($e);
            }

            // Redirect to List
            header(sprintf('Location: %sleads/', URL_BACKEND));
            exit;
        }

        // Render template file
        echo $this->view->render('::pages/leads/lead/delete', [
            'view' => $this->view,
            'lead' => $lead,
            'leadAuth' => $leadAuth,
            'leadId' => $lead->getId(),
        ]);
    }
}
