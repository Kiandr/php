<?php

namespace REW\Backend\Controller\Settings\Partners\Firstcallagent;

use REW\Backend\Auth\PartnersAuth;
use REW\Backend\Partner\Firstcallagent as Partner_Firstcallagent;
use REW\Backend\Controller\AbstractController;
use REW\Backend\Exceptions\UnauthorizedPageException;
use REW\Backend\Exceptions\PageNotFoundException;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\CacheInterface;
use \Backend_Agents;
use \PDOException;
use \Exception;

/**
 * IndexController
 * @package REW\Backend\Controller\Settings\
 */
class IndexController extends AbstractController
{

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var FactoryInterface
     */
    protected $view;

    /**
     * @var NoticesCollectionInterface
     */
    protected $notices;

    /**
     * @var LogInterface
     */
    protected $log;

    /**
     * @var PartnersAuth
     */
    protected $partnersAuth;

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var Partner_Firstcallagent
     */
    protected $firstcallagent;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * IndexController constructor.
     * @param DBInterface $db
     * @param FactoryInterface $view
     * @param NoticesCollectionInterface $notices
     * @param LogInterface $log
     * @param PartnersAuth $partnersAuth
     * @param AuthInterface $auth
     * @param Partner_Firstcallagent $firstcallagent
     * @param Cache $cache
     */
    public function __construct(
        DBInterface $db,
        FactoryInterface $view,
        NoticesCollectionInterface $notices,
        LogInterface $log,
        PartnersAuth $partnersAuth,
        AuthInterface $auth,
        Partner_Firstcallagent $firstcallagent,
        CacheInterface $cache
    ) {
    
        $this->db = $db;
        $this->view = $view;
        $this->notices = $notices;
        $this->log = $log;
        $this->partnersAuth = $partnersAuth;
        $this->auth = $auth;
        $this->firstcallagent = $firstcallagent;
        $this->cache = $cache;
    }

    /**
     * @throws UnauthorizedPageException
     * @throws PageNotFoundException
     */
    public function __invoke()
    {

        // Check that the module is enabled
        if (!$this->firstcallagent->isEnabled()) {
            throw new PageNotFoundException();
        }

        // Authorized to manage First Call Agent settings
        if (!$this->partnersAuth->canViewPartners($this->auth)) {
            throw new UnauthorizedPageException(__('You do not have permission to view settings'));
        }

        $agents = [];
        $account = isset($_POST['setup']) ? true : false;
        
        // Insert or Update the FCA Partner row
        if (isset($_POST['save']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $params = ['sending' => $_POST['sending'], 'api_key' => $_POST['api_key'], 'agent_id' => $this->auth->info('id')];

            if ($this->firstcallagent->getSettings()) {
                $params['exclude_agents'] = isset($_POST['exclude_agents']) ? implode(',', $_POST['exclude_agents']) : '';

                try {
                    $this->updatePartner($params);

                    $this->notices->success(__('Success: FCA has been updated.'));

                    header('Location: ' . URL_BACKEND . 'settings/partners/firstcallagent/');
                    exit;
                } catch (Exception $e) {
                    $this->notices->error(__('Error Occurred: FCA could not be Updated.'));
                }
            } else {
                try {
                    $this->enablePartner($params);

                    $this->notices->success(__('Success: FCA has been enabled.'));

                    header('Location: ' . URL_BACKEND . 'settings/partners/firstcallagent/');
                    exit;
                } catch (Exception $e) {
                    $this->notices->error(__('Error Occurred: FCA could not be enabled.'));
                }
            }
        }

        // Delete the FCA Partner row
        if (isset($_POST['disable']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->disablePartner();

                //Clear lead limit notification cache
                $cacheIndex = 'fca.lead.limit.message';
                $this->cache->deleteCache($cacheIndex);

                // Set success message and save notifications
                $this->notices->success(__('Success: FCA has been disabled.'));

                header('Location: ' . URL_BACKEND . 'settings/partners/firstcallagent/');
                exit;
            } catch (Exception $e) {
                $this->notices->error(__('Error Occurred: FCA could not be deleted.'));
            }
        }

        if ($settings = $this->firstcallagent->getSettings()) {
            $account = true;

            $agents = $this->getAgents();
            $settings['exclude_agents'] = explode(',', $settings['exclude_agents']);
        }

        // Render template file
        echo $this->view->render('::pages/settings/partners/firstcallagent/default', [
            'settings' => $settings,
            'agents' => $agents,
            'account' => $account
        ]);
    }

    /**
     * @return array $agents
     */
    private function getAgents()
    {
        $agents = array();
        $result = $this->db->query("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `agents` ORDER BY `first_name` ASC;");
        while ($row = $result->fetch()) {
            $agents[] = $row;
        }

        return $agents;
    }


    /**
     * array['sending'] string
     * array['api_key'] string
     * array['exclude_agents'] string
     * array['agent_id'] string
     *
     * @param array $params
     * @throws PDOException $e
     */
    private function updatePartner(array $params)
    {
        try {
            $update = $this->db->prepare('UPDATE `partners_firstcallagent` SET `sending` = :sending, `api_key` = :api_key, `exclude_agents` = :exclude_agents WHERE `agent_id` = :agent_id;');
            $update->execute($params);
        } catch (PDOException $e) {
            $this->log->error($e);
            throw $e;
        }
    }


    /**
     * array['sending'] string
     * array['api_key'] string
     * array['agent_id'] string
     *
     * @param array $params
     * @throws PDOException $e
     */
    private function enablePartner(array $params)
    {
        try {
            $enable = $this->db->prepare('INSERT INTO `partners_firstcallagent` SET `sending` = :sending, `api_key` = :api_key, `agent_id` = :agent_id;');
            $enable->execute($params);
        } catch (PDOException $e) {
            $this->log->error($e);
            throw $e;
        }
    }

    /**
     * @throws PDOException $e
     */
    private function disablePartner()
    {

        try {
            $remove = $this->db->prepare('DELETE FROM `partners_firstcallagent` WHERE `agent_id` = :agent_id;');
            $remove->execute(['agent_id' => $this->auth->info('id')]);
        } catch (PDOException $e) {
            $this->log->error($e);
            throw $e;
        }
    }
}
