<?php

namespace REW\Backend\Controller\Settings\Addons;

use REW\Backend\Auth\SettingsAuth;
use REW\Backend\Controller\AbstractController;
use REW\Backend\Exceptions\UnauthorizedPageException;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Core\Interfaces\DBInterface;
use Psr\Http\Message\ServerRequestInterface;
use \PDOException;

/**
 * IndexController
 * @package REW\Backend\Controller\Settings
 */
class IndexController extends AbstractController
{
    /**
     * Twig Template File
     */
    const TEMPLATE_FILE = __DIR__ . '/../../../../assets/views/pages/settings/addons/index.html.twig';

    /**
     * Per-subdomain config options (key: db_value, val: module_config_value)
     */
    const SUBDOMAIN_MODULE_CONFIG_KEYS = [
        ['title' => 'Drive Time', 'key' => 'REW_IDX_DRIVE_TIME', 'value' => 'drivetime']
    ];

    /**
     * @var SettingsAuth
     */
    protected $settingsAuth;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

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
     * @param SettingsAuth $settingsAuth
     * @param SettingsInterface $settings
     * @parma ServerRequestInterface $serverRequest,
     * @param NoticesCollectionInterface $notices
     * @param FactoryInterface $view
     * @param AuthInterface $auth
     * @param LogInterface $log
     * @param DBInterface $db
     */
    public function __construct(
        SettingsAuth $settingsAuth,
        SettingsInterface $settings,
        ServerRequestInterface $request,
        NoticesCollectionInterface $notices,
        FactoryInterface $view,
        AuthInterface $auth,
        LogInterface $log,
        DBInterface $db
    ) {
        $this->settingsAuth = $settingsAuth;
        $this->settings = $settings;
        $this->request = $request;
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
        if (!$this->settingsAuth->canManageCmsSettings($this->auth)) {
            throw new UnauthorizedPageException(__('You do not have permission to view CMS Add-ons settings'));
        }

        $validAddons = $this->getValidAddons();
        if ($this->request->getMethod() === 'POST') {

            // Get Possible Addons
            $body = $this->request->getParsedBody();

            // Get Enabled Addons
            $enabledAddons = [];
            foreach ($validAddons as $validAddon) {
                if ($body[$validAddon['value']]) {
                    $enabledAddons[] = $validAddon['value'];
                }
            }
            $enabledAddons = !empty($enabledAddons) ? implode(',', $enabledAddons) : '';

            // Save Enabled Addons
            try {
                $setAddonsQuery = $this->db->prepare(sprintf(
                    'UPDATE `%s`'
                    . ' SET `cms_addons` = ?'
                    . ' WHERE `id` = 1;',
                    $this->settings->TABLES['LM_AGENTS']
                ));
                $setAddonsQuery->execute([$enabledAddons]);
                $this->notices->success(__('CMS Add-ons updated successfully.'));
            } catch (PDOException $e) {
                $this->notices->error(__('CMS Add-ons could not be updated.'));
                $this->log->error($e);
            }
        }

        // Get Current Addons
        $fetchAddonsQuery = $this->db->prepare(sprintf(
            'SELECT `cms_addons`'
            . ' FROM `%s`'
            . ' WHERE `id` = 1;',
            $this->settings->TABLES['LM_AGENTS']
        ));
        $fetchAddonsQuery->execute();
        $currentAddons = $fetchAddonsQuery->fetchColumn();
        $currentAddons= !empty($currentAddons) ? explode(',', $currentAddons) : [];

        $addons = [];
        foreach ($validAddons as $validAddon) {
            $addons[] = [
                'title' => $validAddon['title'],
                'value' => $validAddon['value'],
                'enabled' => in_array($validAddon['value'], $currentAddons)
            ];
        }

        echo $this->view->render(self::TEMPLATE_FILE, [
            'addons' => $addons
        ]);
    }

    protected function getValidAddons()
    {
        $addons = [];
        foreach (self::SUBDOMAIN_MODULE_CONFIG_KEYS as $module) {
            if ($this->settings->MODULES[$module['key']]) {
                $addons[] = $module;
            }
        }
        return $addons;
    }
}
