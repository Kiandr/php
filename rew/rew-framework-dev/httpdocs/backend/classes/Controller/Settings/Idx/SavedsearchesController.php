<?php

namespace REW\Backend\Controller\Settings\Idx;

use Psr\Http\Message\ServerRequestInterface;
use REW\Backend\Auth\SettingsAuth;
use REW\Backend\Controller\AbstractController;
use REW\Backend\Exceptions\MissingSettingsException;
use REW\Backend\Exceptions\UnauthorizedPageException;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\Page\BackendInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\DatabaseInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\SkinInterface;
use REW\Core\Interfaces\IDXInterface;
use REW\Core\Interfaces\DBInterface;

/**
 * SavedsearchesController
 * @package REW\Backend\Controller\Cms
 */
class SavedsearchesController extends AbstractController
{

    /**
     * @var ServerRequestInterface
     */
    private $serverRequest;

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
     * @var BackendInterface
     */
    protected $page;

    /**
     * @var FactoryInterface
     */
    protected $view;

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var SkinInterface
     */
    protected $skin;

    /**
     * @var IDXInterface
     */
    protected $idx;

    /**
     * @var DatabaseInterface
     */
    protected $dbIdx;

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var Raw post message
     */
    protected $post;

    /**
     * @param ServerRequestInterface $serverRequest
     * @param SettingsAuth $settingsAuth
     * @param NoticesCollectionInterface $notices
     * @param SettingsInterface $settings
     * @param DatabaseInterface $dbIdx
     * @param FactoryInterface $view
     * @param BackendInterface $page
     * @param SkinInterface $skin
     * @param AuthInterface $auth
     * @param IDXInterface $idx
     * @param DBInterface $db
     */
    public function __construct(
        ServerRequestInterface $serverRequest,
        SettingsAuth $settingsAuth,
        NoticesCollectionInterface $notices,
        SettingsInterface $settings,
        DatabaseInterface $dbIdx,
        FactoryInterface $view,
        BackendInterface $page,
        SkinInterface $skin,
        AuthInterface $auth,
        IDXInterface $idx,
        DBInterface $db
    ) {
        $this->serverRequest = $serverRequest;
        $this->settingsAuth = $settingsAuth;
        $this->settings = $settings;
        $this->notices = $notices;
        $this->dbIdx = $dbIdx;
        $this->view = $view;
        $this->page = $page;
        $this->skin = $skin;
        $this->auth = $auth;
        $this->idx = $idx;
        $this->db = $db;
    }

    /**
     * @throws UnauthorizedPageException If not authorized to view page
     * @throws MissingSettingsException If IDX settings failed to load
     */
    public function __invoke()
    {
        // Authorized to manage IDX settings
        if (!$this->settingsAuth->canManageSettings($this->auth)) {
            throw new UnauthorizedPageException(__('You do not have permission to view idx settings'));
        }

        // Save IDX system settings on POST submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->save();
        }

        // Require IDX system settings
        $system = $this->getIdxSettings();

        //Unserialize params
        $params = unserialize($system["savedsearches_responsive_params"]);

        // Get Super Admin Info
        $super_admin = $this->getSuperAdminInfo();

        // Get Offices list
        $offices = $this->getOffices();

        // Resolve path to saved search email template's files
        $savedSearchEmailPath = $this->skin->getSavedSearchEmailPath();

        // Check if responsive saved search email preview template exists
        $previewTemplate = $savedSearchEmailPath  . "preview.php";
        if ($this->view->exists($previewTemplate)) {
            // Render responsive preview
            $responsive_preview = $this->view->render($previewTemplate, [
                "message" => $params["message"]["body"],
                "params" => $params
            ]);
        }

        // Check if responsive saved search email template exists
        $indexTemplate = $savedSearchEmailPath  . "index.php";

        // Render template file
        echo $this->view->render('::pages/settings/idx/savedsearches', [
            'feed' => $this->settings->IDX_FEED,
            'settings' => $this->settings,
            'skin' => $this->skin,
            'page' => $this->page,
            'system' => $system,
            'super_admin' => $super_admin,
            'offices' => $offices,
            'responsive_preview' => $responsive_preview,
            'params' => $params,
            'saved_search_email_responsive_template_exists' => $this->view->exists($indexTemplate)
        ]);

    }

    /**
     * @throws MissingSettingsException
     * @return array
     */
    public function getIdxSettings()
    {
        if (!$system = $this->getIdxSystemSettings()) {
            throw new MissingSettingsException;
        }
        return $system;
    }

    /**
     * @return array
     */
    public function getIdxSystemSettings()
    {
        return $this->db->fetch(sprintf("SELECT `idx`, 
                                           `savedsearches_responsive`, 
                                           `savedsearches_responsive_params`,
                                           `force_savedsearches_responsive`
                                    FROM `%s`
                                    WHERE idx in(:idx, '')
                                    ORDER BY FIELD(idx, :idx, '') LIMIT 1", TABLE_IDX_SYSTEM), [
            "idx" => $this->settings->IDX_FEED
        ]);
    }

    /**
     * @return array
     */

    public function getSuperAdminInfo()
    {
        // Super Admin Details
        return $this->db->fetch("SELECT `id`, `first_name`, `last_name` FROM `" . LM_TABLE_AGENTS . "` WHERE `id` = 1;");
    }

    /**
     * @return array
     */

    public function getOffices()
    {
        // Super Admin Details
        return $this->db->fetchAll("SELECT * FROM `featured_offices` WHERE `display` = 'Y'");
    }
    /**
     * @return boolean true if custom contact details selected
     */
    protected function isCustomContact() {
        return (
            $this->post['params']['sender']['from'] === "custom"
        );
    }

    /**
     * @return boolean true if custom contact details filled in, otherwise false
     */
    protected function validateCustomContact()
    {
        return (
            $this->validCustomName() &&
            $this->validCustomEmail()
        );
    }

    /**
     * @return boolean true if contact name filled in, otherwise false
     */
    protected function validCustomName()
    {
        return (
            !empty($this->post['params']['sender']['name'])
        );
    }

    /**
     * @return boolean true if contact email filled in, otherwise false
     */
    protected function validCustomEmail()
    {
        return (
            !empty($this->post['params']['sender']['email'])
        );
    }

    protected function cleanExit($status, $feed) {
        header(sprintf('Location: ?%s%s', $status, $feed));
        exit;
    }

    public function save()
    {
        try {

            $this->post = $this->serverRequest->getParsedBody();

            // Redirect back to settings form
            $feed = isset($this->post['feed']) ? sprintf('&feed=%s', urlencode($this->post['feed'])) : '';

            if($this->isCustomContact()) {
                $valid_contact = true;
                // Check the Name field
                if(!$this->validCustomName()) {
                    $this->notices->error(__('Custom Sender Name is required to send the email. '));
                    $valid_contact = false;
                }
                // Check the Email field
                if(!$this->validCustomEmail()) {
                    $this->notices->error(__('Custom Sender Email is required to send the email. '));
                    $valid_contact = false;
                }
                if(!$valid_contact) {
                    $this->cleanExit('error', $feed);
                }
            }

            // Save the logo image file on params
            if (!empty($this->post['params']['logo']['id']) && empty($this->post['params']['logo']['file'])) {
                $logo = $this->db->fetch(sprintf("SELECT `file` FROM `%s` WHERE `id` = :id;", $this->settings->TABLES['UPLOADS']),  ["id"=> $this->post['params']['logo']['id']]);
                if (!empty($logo)) {
                    $this->post['params']['logo']['file'] = $logo['file'];
                }
            }

            // The Default IDX System Settings
            $default = $this->getIdxSystemSettings();

            // Generate query string to save IDX settings
            $queryString = sprintf("INSERT INTO `%s` SET
                    `idx`                               = :feed,
                    `savedsearches_responsive`          = :savedsearches_responsive,
                    `savedsearches_responsive_params`   = :savedsearches_responsive_params,
                    `force_savedsearches_responsive`    = :force_savedsearches_responsive,
                    `timestamp_created`                 = NOW()
                    ON DUPLICATE KEY UPDATE
                    `savedsearches_responsive`          = :savedsearches_responsive,
                    `savedsearches_responsive_params`   = :savedsearches_responsive_params,
                    `timestamp_updated`                 = NOW()
                ;", TABLE_IDX_SYSTEM);

            // Query parameters
            $queryParams = [
                'feed'                              => $this->post['feed'],
                'savedsearches_responsive'          => $this->post['savedsearches_responsive'],
                'savedsearches_responsive_params'   => serialize($this->post['params']),
                'force_savedsearches_responsive'    => $default['force_savedsearches_responsive']
            ];

            // Execute database query
            $query = $this->db->prepare($queryString);
            $query->execute($queryParams);

            // Display success notification
            $this->notices->success(__('IDX Settings have successfully been saved.'));

            $this->cleanExit('success', $feed);

            // Database error occurred
        } catch (\PDOException $e) {
            $this->notices->error(__('IDX Settings could not be saved, please try again.'));
        }
    }
}
