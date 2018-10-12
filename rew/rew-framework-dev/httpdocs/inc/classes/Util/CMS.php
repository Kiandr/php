<?php

use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\Util\CMSInterface;
use REW\Core\Interfaces\Factories\DBFactoryInterface;

/**
 * Util_CMS is a utility class containing methods used in our Content Management System
 *
 */
class Util_CMS implements CMSInterface
{
    /**
     * Per-subdomain config options (key: db_value, val: module_config_value)
     */
    const SUBDOMAIN_MODULE_CONFIG_KEYS = [
        ['title' => 'Drive Time', 'module_key' => 'REW_IDX_DRIVE_TIME', 'db_val' => 'drivetime']
    ];

    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * @var DBFactoryInterface
     */
    private $dbFactory;

    /**
     * @var HooksInterface
     */
    private $hooks;

    /**
     * Util_CMS constructor.
     * @param SettingsInterface $settings
     * @param DBFactoryInterface $dbFactory
     * @param HooksInterface $hooks
     */
    public function __construct(SettingsInterface $settings, DBFactoryInterface $dbFactory, HooksInterface $hooks)
    {
        $this->settings = $settings;
        $this->dbFactory = $dbFactory;
        $this->hooks = $hooks;
    }

    /**
     * PPC Settings
     * @var array
     */
    protected $ppc;

    /**
     * Check Redirect Rules
     *
     * @return void
     */
    public function checkRedirects()
    {

        if (PHP_SAPI === 'cli') {
            return;
        }

        $timer = Profile::timer()->stopwatch(__METHOD__)->start();

        // 301 Redirects for Main Site (REW Rewrite Module)
        if (!empty($this->settings->MODULES['REW_REWRITE_MANAGER']) && $this->settings->SETTINGS['agent'] == 1) {
            // Check if can redirect
            $uri = $_SERVER['REQUEST_URI'];
            $url = strstr($uri, '?') !== false ? substr($uri, 0, strpos($uri, '?')) : $uri;
            if (in_array($url, array(
                '/',
                '/idx/',
                '/idx/map/',
                '/idx/login.html',
                '/idx/remind.html',
                '/idx/verify.html',
                '/idx/connect.html',
                '/idx/register.html',
                '/idx/dashboard.html',
                '/idx/sitemap.html',
                '/directory/',
                '/blog/'
            ))) {
                $timer->stop();
                return;
            }

            // Don't redirect /backend/
            if (strpos($url, '/backend/') === 0) {
                $timer->stop();
                return;
            }

            // Find redirect rule
            $db = $this->dbFactory->get('cms');
            $redirect = $db->prepare("SELECT `new` FROM `pages_rewrites` WHERE BINARY `old` = :url LIMIT 1;");
            $redirect->execute(array('url' => $uri));
            $redirect = $redirect->fetch();
            if (!empty($redirect)) {
                if (strpos($redirect['new'], '/') === 0) {
                    $redirect['new'] = Settings::getInstance()->SETTINGS['URL_RAW'] . $redirect['new'];
                }
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: ' . $redirect['new']);
                exit;
            }
        }

        $timer->stop();
    }

    /**
     * Check Allowed Subdomains, 301 If Not 'www' or Agent CMS Site
     *
     * @return void
     */
    public function checkSubdomain()
    {

        $timer = Profile::timer()->stopwatch(__METHOD__)->start();

        // Get Subdomain
        $subdomain = Http_Host::getSubdomain();

        // Redirection
        $redirect = false;

        // CMS Database
        $db = $this->dbFactory->get('cms');

        // Agent Subdomains Enabled
        if (!empty($this->settings['MODULES']['REW_AGENT_CMS']) || !empty($this->settings['MODULES']['REW_TEAM_CMS'])) {
            // Check Sub-Domain
            if (!in_array($subdomain, array((Http_Host::getDev() ? '' : 'www')))) {

                // Strip www. from Agent Sites
                if (preg_match('/^www\./', $subdomain)) {
                    $subdomain = preg_replace('/^www\./', '', $subdomain);

                    // Check Agent Subdomain
                    if (!empty($this->settings['MODULES']['REW_AGENT_CMS'])) {
                        // Locate Agent
                        $agent = $db->prepare("SELECT `cms_link` FROM `agents` WHERE `cms` = 'true' AND `cms_link` = :cms_link;");
                        $agent->execute(array('cms_link' => $subdomain));
                        $agent = $agent->fetch();

                        // Agent Found, Re-Direct to Agent Site
                        if (!empty($agent)) {
                            $redirect = sprintf(Settings::getInstance()->SETTINGS['URL_AGENT_SITE_RAW'], $agent['cms_link']) . $_SERVER['REQUEST_URI'];
                        }
                    }

                    // Check Team Subdomain
                    if (!empty($this->settings->MODULES['REW_TEAM_CMS'])) {
                        // Locate Team
                        $team = $db->prepare("SELECT `subdomain_link` FROM `teams` WHERE `subdomain` = 'true' AND `subdomain_link` = :subdomain_link;");
                        $team->execute(array('subdomain_link' => $subdomain));
                        $team = $team->fetch();


                        // Team Found, Re-Direct to Team Site
                        if (!empty($team)) {
                            $redirect = sprintf(Settings::getInstance()->SETTINGS['URL_AGENT_SITE_RAW'], $team['subdomain_link']) . $_SERVER['REQUEST_URI'];
                        }
                    }

                    // Neither Team nor Agent were Found, Re-Direct to Main Site
                    if (empty($redirect)) {
                        $redirect = Settings::getInstance()->SETTINGS['URL_RAW'] . $_SERVER['REQUEST_URI'];
                    }

                // Backend only accessible from main subdomain
                } else if (strpos($_SERVER['REQUEST_URI'], '/backend/') === 0) {
                    $redirect = Settings::getInstance()->SETTINGS['URL_RAW'] . $_SERVER['REQUEST_URI'];
                } else {
                    //Has a subdomain been found
                    $subdomain_found = false;

                    // Check for Agent
                    if (!empty($this->settings['MODULES']['REW_AGENT_CMS'])) {
                        $agent = $db->prepare("SELECT * FROM `agents` WHERE `cms` = 'true' AND `cms_link` = :cms_link;");
                        $agent->execute(array('cms_link' => $subdomain));
                        $agent = $agent->fetch();

                        // Agent Site Found
                        if (!empty($agent)) {
                            $subdomain_found = true;
                            $this->settings['SETTINGS']['agent'] = $agent['id'];
                            $this->settings['SETTINGS']['agent_idxs'] = !empty($agent['cms_idxs']) ? explode(",", $agent['cms_idxs']) : array();
                        }
                    }

                    // Check for Team
                    if (!empty($this->settings['MODULES']['REW_TEAM_CMS']) && !$subdomain_found) {
                        $team = $db->prepare("SELECT * FROM `teams` WHERE `subdomain` = 'true' AND `subdomain_link` = :subdomain_link;");
                        $team->execute(array('subdomain_link' => $subdomain));
                        $team = $team->fetch();

                        // Team Site Found
                        if (!empty($team)) {
                            $subdomain_found = true;
                            unset($this->settings['SETTINGS']['agent']);
                            $this->settings['SETTINGS']['team'] = $team['id'];
                            $this->settings['SETTINGS']['team_idxs'] = !empty($team['subdomain_idxs']) ? explode(",", $team['subdomain_idxs']) : array();
                        }
                    }

                    if (!$subdomain_found) {
                        // Redirect Address
                        $redirect = Settings::getInstance()->SETTINGS['URL_RAW'] . $_SERVER['REQUEST_URI'];
                    }
                }
            }

        // Only Allow www. (Unless on dev server)
        } elseif (!in_array($subdomain, array((Http_Host::getDev() ? '' : 'www')))) {
            $redirect = Settings::getInstance()->SETTINGS['URL_RAW'] . $_SERVER['REQUEST_URI'];
        }

        // Toggle primary config based on specific subdomain config (module addons)
        if (!empty($this->settings['SETTINGS']['team'])) {
            $config = $db->fetch("SELECT `subdomain_addons` FROM `teams` WHERE `id` = :id", ['id' => $this->settings['SETTINGS']['team']]);
            $addons = explode(',', $config['subdomain_addons']);
        } else {
            $config = $db->fetch("SELECT `cms_addons` FROM `agents` WHERE `id` = :id", ['id' => $this->settings['SETTINGS']['agent']]);
            $addons = explode(',', $config['cms_addons']);
        }
        $this->settings['ADDONS'] = [];
        foreach (self::SUBDOMAIN_MODULE_CONFIG_KEYS as $config) {
            if (in_array($config['db_val'], $addons)) {
                $this->settings['ADDONS'][] = $config['db_val'];
            }
        }

        // 301 Redirection
        if (!empty($redirect)) {
            $currentHost = sprintf(
                '%s://%s%s',
                $_SERVER['REQUEST_SCHEME'],
                $_SERVER['SERVER_NAME'],
                $_SERVER['REQUEST_URI']
            );
            // Prevent infinite redirect for non-existent subdomains
            if ($redirect === $currentHost) {
                $redirect = $this->settings['urls']['URL_DOMAIN'] . ltrim($_SERVER['REQUEST_URI'], '/');
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: ' . $redirect);
                exit;
            }
        }

        $this->hooks->hook(Hooks::HOOK_AGENT_INFO_LOADED)->run();

        $timer->stop();
    }

    /**
     * Load PPC Settings from Database
     * @return array
     */
    public function getPPCSettings()
    {

        if (!$this instanceof self) {
            return Container::getInstance()->make(CMSInterface::class)->getPPCSettings();
        }

        // PCC Settings Already Loaded
        if ($this->ppc) {
            return $this->ppc;
        }

        // Conversion tracking is disabled
        if (empty($this->settings->MODULES['REW_CONVERSION_TRACKING'])) {
            return array();
        }

        $timer = Profile::timer()->stopwatch(__METHOD__)->start();

        // CMS Database
        $db = $this->dbFactory->get('cms');

        // PPC Settings
        $settings = $db->prepare("SELECT `settings` FROM `default_info` WHERE `agent` <=> :agent AND `team` <=> :team;");
        $settings->execute(array(
            'agent' => $this->settings->SETTINGS['agent'],
            'team'  => $this->settings->SETTINGS['team']
        ));
        $settings = $settings->fetchColumn();
        if (!empty($settings)) {
            // Load Settings
            $settings = unserialize($settings);

            // PPC Settings
            $settings = !empty($settings) && is_array($settings) ? $settings : array(
                // PPC Settings
                'ppc' => array(
                    'enabled'       => 'false',
                    'idx-register'  => null,
                    'idx-inquire'   => null,
                    'idx-showing'   => null,
                    'idx-phone'     => null,
                    'form-contact'  => null,
                    'form-buyers'   => null,
                    'form-seller'   => null,
                    'form-approve'  => null,
                    'form-cma'      => null
                )
            );

            $timer->stop();

            // Return PPC Settings
            return $this->ppc = $settings['ppc'];
        }

        $timer->stop();

        // Empty settings
        return array();
    }
}
