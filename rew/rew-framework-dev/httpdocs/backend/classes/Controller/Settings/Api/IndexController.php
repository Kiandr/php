<?php

namespace REW\Backend\Controller\Settings\Api;

use REW\Backend\Auth\SettingsAuth;
use REW\Backend\Controller\AbstractController;
use REW\Backend\Exceptions\UnauthorizedPageException;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Core\Interfaces\DBInterface;
use \PDOException;

/**
 * IndexController
 * @package REW\Backend\Controller\Settings
 */
class IndexController extends AbstractController
{
    /**
     * @var SettingsAuth
     */
    protected $settingsAuth;

    /**
     * @var SettingsInterface
     */
    protected $settings;

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
     * @var array
     */
    private $_success = [];

    /**
     * @var array
     */
    private $_errors = [];

    /**
     * @var int
     */
    private $page_limit = 25;

    /**
     * @param SettingsAuth $settingsAuth
     * @param SettingsInterface $settings
     * @param NoticesCollectionInterface $notices
     * @param FactoryInterface $view
     * @param AuthInterface $auth
     * @param LogInterface $log
     * @param DBInterface $db
     */
    public function __construct(
        SettingsAuth $settingsAuth,
        SettingsInterface $settings,
        NoticesCollectionInterface $notices,
        FactoryInterface $view,
        AuthInterface $auth,
        LogInterface $log,
        DBInterface $db
    ) {
        $this->settingsAuth = $settingsAuth;
        $this->settings = $settings;
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

        // Handle POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Delete application
            if (!empty($_POST['delete']) && $_POST['delete'] != 1) {
                $remove_app = $this->removeApplication($_POST['delete']);
                $this->auth->setNotices($this->_success, $this->_errors);

                // Require application
                if ($remove_app) {
                    // Redirect
                    header('Location: ?');
                    exit;
                }
            }
        }

        // Get count
        $count_apps = $this->getCount();

        // Query String
        list(, $query) = explode('?', $_SERVER['REQUEST_URI'], 2);
        parse_str($query, $query_string);

        // Page Limit
        $page_limit = $this->page_limit;

        // Search Limit
        if ($count_apps['total'] > $this->page_limit) {
            $limitvalue = (($_GET['p'] - 1) * $page_limit);
            $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
            $sql_limit  = " LIMIT " . $limitvalue . ", " . $page_limit;
        }

        // Pagination
        $pagination = generate_pagination($count_apps['total'], $_GET['p'], $page_limit, $query_string);

        // Render template file
        echo $this->view->render('::pages/settings/api/default', [
            'applications' => $this->getApplications($sql_limit),
            'view' => $this->view,
            'pagination' => $pagination,
            'settings' => $this->settings
        ]);
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        try {
            return $this->db->fetch('SELECT COUNT(`id`) AS \'total\' FROM `api_applications`;');
        } catch (PDOException $e) {
            $this->log->error($e);
            return null;
        }
    }

    /**
     * @param string $sql_limit
     * @return mixed
     */
    public function getApplications($sql_limit)
    {
        try {
            $queryString = sprintf('SELECT *, UNIX_TIMESTAMP(`timestamp`) AS `timestamp_created`, (`num_requests_ok` + `num_requests_error`) AS `num_requests` FROM `api_applications` %s;', $sql_limit);
            return $this->db->fetchAll($queryString);
        } catch (PDOException $e) {
            $this->log->error($e);
            return null;
        }
    }

    /**
     * @param string $id
     * @return bool
     */
    public function removeApplication($id)
    {
        try {
            $queryString = "SELECT `id` FROM `api_applications` WHERE `id` = ?;";
            $query = $this->db->prepare($queryString);
            if ($query->execute([$id])) {
                $queryString = "DELETE FROM `api_applications` WHERE `id` = ?;";
                $query = $this->db->prepare($queryString);
                if ($query->execute([$id])) {
                    $this->_success[] = __('The application has been successfully deleted.');
                    return true;
                }
            }
        } catch (PDOException $e) {
            $this->log->error($e);
        }
        $this->_errors[] = __('We are unable to delete this application.');
        return false;
    }
}
