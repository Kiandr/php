<?php

namespace REW\Backend\Controller\Settings\Api;

use REW\Backend\Auth\SettingsAuth;
use REW\Backend\Controller\AbstractController;
use REW\Backend\Exceptions\UnauthorizedPageException;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Hook\REW\OutgoingAPI;
use REW\Core\Interfaces\LogInterface;
use PDOException;

/**
 * OutgoingController
 * @package REW\Backend\Controller\Settings
 */
class OutgoingController extends AbstractController
{
    /**
     * @var SettingsAuth
     */
    protected $settingsAuth;

    /**
     * @var NoticesCollectionInterface
     */
    protected $notices;

    /**
     * @var FactoryInterface
     */
    protected $view;

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var LogInterface
     */
    protected $log;

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var array $_success
     */
    private $_success = [];

    /**
     * @var array $_errors
     */
    private $_errors = [];

    /**
     * @param SettingsAuth $settingsAuth
     * @param NoticesCollectionInterface $notices
     * @param FactoryInterface $view
     * @param AuthInterface $auth
     * @param LogInterface $log
     * @param DBInterface $db
     */
    public function __construct(
        SettingsAuth $settingsAuth,
        NoticesCollectionInterface $notices,
        FactoryInterface $view,
        AuthInterface $auth,
        LogInterface $log,
        DBInterface $db
    ) {
        $this->settingsAuth = $settingsAuth;
        $this->notices = $notices;
        $this->view = $view;
        $this->auth = $auth;
        $this->log = $log;
        $this->db = $db;
    }

    /**
     * @throws UnauthorizedPageException If not authorized to view page
     */
    public function __invoke()
    {
        // Authorized to manage API settings
        if (!$this->settingsAuth->canManageApi($this->auth)) {
            throw new UnauthorizedPageException(__('You do not have permission to view API settings'));
        }

        // Partners config
        $partners = $this->auth->info('partners');

        // Existing destinations
        $destinations = is_array($partners) && !empty($partners['outgoing_api']['destinations']) ? $partners['outgoing_api']['destinations'] : array();

        // remove destination
        if (!empty($_POST['delete'])) {
            foreach ($destinations as $k => $dest) {
                if ($k + 1 == $_POST['delete']) {
                    unset($destinations[$k]);
                    $destinations = array_values($destinations);

                    // Merge changes
                    $partners = array_merge($this->auth->info('partners'), array(
                        'outgoing_api' => array('destinations' => $destinations),
                    ));

                    // update agent partners column
                    $remove_dest = $this->removeDestination($partners, $_POST['delete']);
                    $this->auth->setNotices($this->_success, $this->_errors);

                    if ($remove_dest) {
                        // Redirect
                        header('Location: ?');
                        exit;
                    }
                    break;
                }
            }
        }

        // Render template file
        echo $this->view->render('::pages/settings/api/outgoing/default', [
            'destinations' => $this->destination_events($destinations),
            'type_rew' => \Hook_REW_OutgoingAPI::DESTINATION_TYPE_REW
        ]);
    }

    /**
     * @param array $destinations
     * @return array
     */
    public function destination_events(array $destinations)
    {
        foreach ($destinations as $k => $dest) {
            $destination_events = array();
            $supported_events = \Hook_REW_OutgoingAPI::getSupportedEventsForDestination($dest['type']);
            if ($dest['type'] == \Hook_REW_OutgoingAPI::DESTINATION_TYPE_REW) {
                foreach ($dest['events'] as $event) {
                    foreach ($supported_events as $e) {
                        if ($e['value'] == $event) {
                            $destination_events[] = $e['title'];
                        }
                    }
                }
            } else if ($dest['type'] == \Hook_REW_OutgoingAPI::DESTINATION_TYPE_CUSTOM) {
                foreach ($dest['events'] as $value => $event) {
                    if ($event['enabled'] !== 'Y') {
                        continue;
                    }
                    foreach ($supported_events as $e) {
                        if ($e['value'] == $value) {
                            $destination_events[] = $e['title'];
                        }
                    }
                }
            }
            $destinations[$k]['events_human'] = $destination_events;
        }
        return $destinations;
    }

    /**
     * @param array $partners
     * @param string $delete
     * @return bool
     */
    public function removeDestination($partners, $delete)
    {
        try {
            $query = $this->db->prepare('UPDATE `agents` SET `partners` = ? WHERE `id` = ?;');
            if ($query->execute([json_encode($partners), $delete])) {
                $this->_success[] = __('Destination has been successfully deleted.');
                return true;
            }
        } catch (PDOException $e) {
            $this->log->error($e);
        }
        $this->_errors[] = __('We are unable to delete this destination.');
        return false;
    }
}
