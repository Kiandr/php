<?php

namespace REW\Backend\Controller\Settings\Idx;

use REW\Backend\Auth\SettingsAuth;
use REW\Backend\Controller\AbstractController;
use REW\Backend\Exceptions\MissingSettingsException;
use REW\Backend\Exceptions\UnauthorizedPageException;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\Page\BackendInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\DatabaseInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\SkinInterface;
use REW\Core\Interfaces\IDXInterface;
use REW\Core\Interfaces\DBInterface;

/**
 * IndexController
 * @package REW\Backend\Controller\Cms
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
     * @var HooksInterface
     */
    protected $hooks;

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
     * @param SettingsAuth $settingsAuth
     * @param NoticesCollectionInterface $notices
     * @param SettingsInterface $settings
     * @param DatabaseInterface $dbIdx
     * @param FactoryInterface $view
     * @param BackendInterface $page
     * @param HooksInterface $hooks
     * @param SkinInterface $skin
     * @param AuthInterface $auth
     * @param IDXInterface $idx
     * @param DBInterface $db
     */
    public function __construct(
        SettingsAuth $settingsAuth,
        NoticesCollectionInterface $notices,
        SettingsInterface $settings,
        DatabaseInterface $dbIdx,
        FactoryInterface $view,
        BackendInterface $page,
        HooksInterface $hooks,
        SkinInterface $skin,
        AuthInterface $auth,
        IDXInterface $idx,
        DBInterface $db
    ) {
        $this->settingsAuth = $settingsAuth;
        $this->settings = $settings;
        $this->notices = $notices;
        $this->dbIdx = $dbIdx;
        $this->hooks = $hooks;
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

        // Require IDX system settings
        $system = $this->getIdxSettings();

        // Save IDX system settings on POST submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Require ENUM false for registration settings
                if (!in_array($_POST['registration'], ['optional', 'false', 'true'])) {
                    $_POST['registration'] = intval($_POST['registration_views'] != 0) ? $_POST['registration_views'] : 'false';
                }

                // Generate query string to save IDX settings
                $queryString = sprintf("INSERT INTO `%s` SET
                    `idx`                       = :feed,
                    `search_cities`             = :search_cities,
                    `copy_register`             = :copy_register,
                    `copy_login`                = :copy_login,
                    `copy_connect`              = :copy_connect,
                    `registration`              = :registration,
                    `registration_required`     = :registration_required,
                    `registration_on_more_pics` = :registration_on_more_pics,
                    `registration_password`     = :registration_password,
                    `registration_phone`        = :registration_phone,
                    `registration_verify`       = :registration_verify,
                    `default_contact_method`    = :default_contact_method,
                    `savedsearches_message`     = :savedsearches_message,
                    `timestamp_created`         = NOW()
                    ON DUPLICATE KEY UPDATE
                    `search_cities`             = :search_cities,
                    `copy_register`             = :copy_register,
                    `copy_login`                = :copy_login,
                    `copy_connect`              = :copy_connect,
                    `registration`              = :registration,
                    `registration_required`     = :registration_required,
                    `registration_on_more_pics` = :registration_on_more_pics,
                    `registration_password`     = :registration_password,
                    `registration_phone`        = :registration_phone,
                    `registration_verify`       = :registration_verify,
                    `default_contact_method`    = :default_contact_method,
                    `savedsearches_message`     = :savedsearches_message,
                    `timestamp_updated`         = NOW()
                ;", TABLE_IDX_SYSTEM);

                // Query parameters
                $queryParams = [
                    'feed'                      => $_POST['feed'],
                    'search_cities'             => (string) (is_array($_POST['search_cities']) ? implode(',', $_POST['search_cities']) : $_POST['search_cities']),
                    'copy_register'             => (string) $_POST['copy_register'],
                    'copy_login'                => (string) $_POST['copy_login'],
                    'copy_connect'              => (string) $_POST['copy_connect'],
                    'registration'              => $_POST['registration'],
                    'registration_required'     => $_POST['registration_required'],
                    'registration_on_more_pics' => $_POST['registration_on_more_pics'] === 'true' ? 'true' : 'false',
                    'registration_password'     => $_POST['registration_password'] === 'true' ? 'true' : 'false',
                    'registration_phone'        => $_POST['registration_phone'] === 'true' ? 'true' : 'false',
                    'registration_verify'       => $_POST['registration_verify'] === 'true' ? 'true' : 'false',
                    'default_contact_method'    => $_POST['default_contact_method'],
                    'savedsearches_message'     => $_POST['savedsearches_message'],
                ];

                // Execute database query
                $query = $this->db->prepare($queryString);
                $query->execute($queryParams);

                // Display success notification
                $this->notices->success(__('IDX Settings have successfully been saved.'));

                // Run hook indicating IDX setting has been saved
                $hook = HooksInterface::HOOK_IDX_SETTING_MANAGER_SAVE;
                $this->hooks->hook($hook)->run($system, $_POST);

                // Redirect back to settings form
                $feed = isset($_POST['feed']) ? sprintf('&feed=%s', urlencode($_POST['feed'])) : '';
                header(sprintf('Location: ?success%s', $feed));
                exit;

            // Database error occurred
            } catch (\PDOException $e) {
                $this->notices->error(__('IDX Settings could not be saved, please try again.'));
            }
        }

        // Ensure city list is an array
        if (!is_array($system['search_cities'])) {
            $system['search_cities'] = explode(',', $system['search_cities']);
        }

        // Run hook indicating IDX Setting has been loaded
        $hook = HooksInterface::HOOK_IDX_SETTING_MANAGER_LOAD;
        $this->hooks->hook($hook)->run($system);

        // Check if responsive saved search email template exists
        $skin_directory = $this->skin->getDirectory();
        $indexTemplate = $this->skin->getSavedSearchEmailPath() . "index.php";

        // Render template file
        echo $this->view->render('::pages/settings/idx/default', [
            'feed' => $this->settings->IDX_FEED,
            'cities' => $this->getIdxCityList(),
            'settings' => $this->settings,
            'skin' => $this->skin,
            'page' => $this->page,
            'system' => $system,
            'saved_search_email_responsive_template_exists' => $this->view->exists($indexTemplate)
        ]);
    }

    /**
     * @throws MissingSettingsException
     * @return array
     */
    public function getIdxSettings()
    {
        $defaults = $this->getIdxDefaultSettings();
        if (!$system = $this->getIdxSystemSettings()) {
            throw new MissingSettingsException;
        }
        return array_merge($defaults, $system);
    }

    /**
     * @return array
     */
    public function getIdxSystemSettings()
    {
        $queryString = "SELECT * FROM `%s` WHERE `idx` = ? LIMIT 1";
        $query = $this->db->prepare(sprintf($queryString, TABLE_IDX_SYSTEM));
        $query->execute([$this->settings->IDX_FEED]);
        if ($settings = $query->fetch()) {
            return $settings;
        }
        $query->execute(['']);
        return $query->fetch();
    }

    /**
     * @return array
     */
    public function getIdxDefaultSettings()
    {
        return [
            'copy_login' => $this->settings->SETTINGS['copy_login'],
            'copy_connect' => $this->settings->SETTINGS['copy_connect'],
            'copy_register' => $this->settings->SETTINGS['copy_register'],
            'default_contact_method' => $this->settings->SETTINGS['default_contact_method'],
            'registration_required' => $this->settings->SETTINGS['registration_required'],
            'registration_password' => $this->settings->SETTINGS['registration_password'],
            'registration_verify' => $this->settings->SETTINGS['registration_verify'],
            'registration_phone' => $this->settings->SETTINGS['registration_phone']
        ];
    }

    /**
     * @return array
     */
    public function getIdxCityList()
    {
        $cityList = [];
        $cityField = sprintf('`%s`', $this->idx->field('AddressCity'));
        $sqlWhere = sprintf('%1$s IS NOT NULL AND %1$s != \'\'', $cityField);
        $this->idx->executeSearchWhereCallback($sqlWhere);
        $queryString = "SELECT DISTINCT %s AS `city` FROM `%s` WHERE %s ORDER BY %s ASC";
        $queryString = sprintf($queryString, $cityField, $this->idx->getTable(), $sqlWhere, $cityField);
        if ($query = $this->dbIdx->query($queryString)) {
            while ($option = $query->fetch_assoc()) {
                $cityList[] = [
                    'title' => ucwords(strtolower($option['city'])),
                    'value' => $option['city']
                ];
            }
        }
        return $cityList;
    }
}
