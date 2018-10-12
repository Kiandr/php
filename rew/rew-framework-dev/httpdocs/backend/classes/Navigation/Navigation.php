<?php

namespace REW\Backend\Navigation;

use REW\Backend\Navigation\Interfaces\NavigationInterface;
use \Exception;

/**
 * Class Navigation
 *
 * @category Navigation
 * @package  REW\Backend\Navigation
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
abstract class Navigation implements NavigationInterface
{

    /**
     * Is this Menu Enabled
     * @var bool
     */
    protected $isEnabled;

    /**
     * Content Landing Page
     * @var bool
     */
    protected $landingPage;

    /**
     * Navigation Links
     * @var array
     */
    protected $navigation;

    /**
     * Navigation Add Links
     * @var array
     */
    protected $addLinks;

    /**
     * Is Content Enabled
     * @return bool
     */
    public function isEnabled()
    {
        if (!isset($this->isEnabled)) {
            $navs = $this->getNavLinks();
            $this->isEnabled = !empty($navs);
        }
        return $this->isEnabled;
    }

    /**
     * Get Content Landing Link
     * @return string|false
     */
    public function getLandingLink()
    {

        // Return Cached Landing Page
        if (isset($this->landingPage)) {
            return $this->landingPage;
        }

        // Get Navigation
        if (!isset($this->navigation)) {
            $this->navigation = $this->loadNavLinks();
        }
        $navigation = $this->navigation;

        // Return Loaded Landing Page
        $landingPage = $navigation[0];
        $this->landingPage = !empty($landingPage) ? $landingPage['link'] : false;
        return $this->landingPage;
    }

    /**
     * Get Navigation Options
     * @param string|null $app
     * @param string|null $app_page
     * @return array
     */
    public function getNavLinks($app = null, $app_page = null)
    {

        // Get Navigation
        if (!isset($this->navigation)) {
            $this->navigation = $this->loadNavLinks();
        }
        $navigation = $this->navigation;

        // Select Current Navigation
        if (isset($app) || isset($app_page)) {
            $navigation = array_map(function ($section) use ($app, $app_page) {
                $section['current'] = null;

                // Check App Page
                if (isset($section['page'])) {
                    if (!is_array($section['page'])) {
                        // Check for Negative
                        $not_page = substr($section['page'], 0, 1) == '!';
                        $section['page'] = ltrim($section['page'], '!');

                        // Check for Match
                        $match =  !empty($section['page'])
                            ? preg_match('/' . preg_quote($section['page'], '/') . '[\/a-zA-Z\-_]*/', $app_page)
                            : $section['page'] == $app_page;
                        $section['current'] = (($not_page && !$match) || (!$not_page && $match));
                    } else {
                        $pages = [];
                        $not_pages = [];
                        foreach ($section['page'] as $section_page) {
                            // Check for match
                            if (substr($section_page, 0, 1) == '!') {
                                $section_page = ltrim($section_page, '!');

                                $match  = !empty($section_page)
                                ? preg_match('/' . preg_quote($section_page, '/') . '[\/a-zA-Z\-_]*/', $app_page)
                                : $section_page == $app_page;
                                if ($match) {
                                    $section['current'] = false;
                                }
                            } else if (!isset($section['current'])) {
                                $match  = !empty($section_page)
                                ? preg_match('/' . preg_quote($section_page, '/') . '[\/a-zA-Z\-_]*/', $app_page)
                                : $section_page == $app_page;
                                if ($match) {
                                    $section['current'] = true;
                                }
                            }
                        }
                        if (!isset($section['current'])) {
                            $section['current'] = false;
                        }
                    }
                }

                // Check App
                if (isset($section['app'])) {
                    if (!is_array($section['app'])) {
                        $not_app = substr($section['app'], 0, 1) == '!';
                        $section['app'] = ltrim($section['app'], '!');
                        $section['current'] =
                            (!isset($section['current']) || $section['current'] == true)
                            && (($not_app && $app != $section['app'])
                            || (!$not_app && $app == $section['app']));
                    } else {
                        $apps = [];
                        $not_apps = [];
                        foreach ($section['app'] as $section_app) {
                            if (substr($section_app, 0, 1) == '!') {
                                $not_apps []= ltrim($section_app, '!');
                            } else {
                                $apps []= $section_app;
                            }
                        }
                        $section['current'] =
                            (!isset($section['current']) || $section['current'] == true)
                            && (in_array($app, $apps) && !in_array($app, $not_apps));
                    }
                }
                return $section;
            }, $navigation);
        }
        return $navigation;
    }

    /**
     * Get add content links
     * @return array
     */
    public function getAddLinks()
    {
        if (!isset($this->addLinks)) {
            $this->addLinks = $this->loadAddLinks();
        }
        return $this->addLinks;
    }

    /**
     * @return string
     */
    public function getNavName()
    {
        $className = get_class($this);
        $navName = substr($className, strrpos($className, '\\') + 1);
        $navName = preg_replace('/Navigation$/', '', $navName);
        return strtolower($navName);
    }

    /**
     * Load Navigation Links
     * @return array
     */
    abstract protected function loadNavLinks();

    /**
     * Load Add Links
     * @return array
     */
    abstract protected function loadAddLinks();

    /**
     * Get current subdomain from cache or load
     * @param string|null $checkPermission
     * @return SubdomainInterface|NULL
     */
    protected function getCurrentSubdomain($checkPermission = null)
    {

        // Load Subdomain
        if (!isset($this->subdomain)) {
            $this->subdomain = $this->loadCurrentSubdomain($checkPermission);
        }
        return $this->subdomain ?: null;
    }

    /**
     * Load current subdomain to manage (if any)
     * @param string|null $checkPermission
     * @return SubdomainInterface|NULL
     */
    protected function loadCurrentSubdomain($checkPermission)
    {
        try {
            $subdomain = $this->subdomainFactory->buildSubdomainFromRequest($checkPermission);
            if ($subdomain) {
                $subdomain->validateSettings();
            } else {
                throw new Exception('No subdomain found');
            }
        } catch (Exception $e) {
            $subdomain = $this->subdomainFactory->buildDefaultSubdomain($checkPermission);
        }
        return $subdomain;
    }
}
