<?php

namespace REW\Backend\Navigation;

use REW\Backend\CMS\Interfaces\SubdomainFactoryInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Backend\CMS\Interfaces\SubdomainInterface;
use REW\Backend\Auth\ContentAuth;
use REW\Backend\Auth\BlogsAuth;
use REW\Backend\Auth\ToolsAuth;
use REW\Backend\Auth\DomainAuth;

/**
 * Class ContentNavigation
 *
 * @category Navigation
 * @package  REW\Backend\Navigation
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class ContentNavigation extends Navigation
{

    /**
     * User to get content nv for
     * @var AuthInterface
     */
    protected $auth;

    /**
     * Backend Settings
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * Content Auth
     * @var ContentAuth
     */
    protected $contentAuth;

    /**
     * Blog Auth
     * @var BlogsAuth
     */
    protected $blogsAuth;

    /**
     * Tools Auth
     * @var ToolsAuth
     */
    protected $toolsAuth;

    /**
     * Domain Auth
     * @var DomainAuth
     */
    protected $domainAuth;

    /**
     * Subdomain Factory
     * @var SubdomainFactoryInterface
     */
    protected $subdomainFactory;

    /**
     * Current Subdomain
     * @var SubdomainInterface
     */
    protected $subdomain;

    /**
     * Construct Content Navigation
     * @param AuthInterface auth;
     * @param ContentAuth $contentAuth
     * @param BlogsAuth $blogsAuth
     * @param ToolsAuth $toolsAuth
     * @param DomainAuth $domainAuth
     * @param SubdomainFactoryInterface $subdomainFactory
     */
    public function __construct(AuthInterface $auth, SettingsInterface $settings, ContentAuth $contentAuth, BlogsAuth $blogsAuth, ToolsAuth $toolsAuth, DomainAuth $domainAuth, SubdomainFactoryInterface $subdomainFactory)
    {
        $this->auth = $auth;
        $this->settings = $settings;
        $this->contentAuth = $contentAuth;
        $this->blogsAuth = $blogsAuth;
        $this->toolsAuth = $toolsAuth;
        $this->domainAuth = $domainAuth;
        $this->subdomainFactory = $subdomainFactory;
    }

    /**
     * Load Navigation Links
     * @return array
     */
    protected function loadNavLinks()
    {

        // Navigation: Content
        $navigation = [];

        // Check if content can be displayed
        $subdomain = $this->getCurrentSubdomain('canManagePages');
        $subdomainAuth = $subdomain ? $subdomain->getAuth() : $this->domainAuth;
        $subdomainPostLink = $subdomain ? $subdomain->getPostLink() : '';

        // Navigation: Pages
        if ($subdomainAuth->canManagePages() || $subdomainAuth->canManageHomepage()) {
            $navigation []= [
                'title' => __('Pages'),
                'link' => $this->settings->URLS['URL_BACKEND'] . 'cms/' . $subdomainPostLink,
                'icon' => 'page',
                'app' => 'cms',
                'page' => $cmsNav ? [] : ['', '!snippets', '!links']
            ];
        }

        // Navigation: Snippets
        if ($subdomainAuth->canManageSnippets()) {
            $navigation []= [
                'title' => __('Snippets'),
                'link' => $this->settings->URLS['URL_BACKEND'] . 'cms/snippets/' . $subdomainPostLink,
                'icon' => 'snippet',
                'app' => ['cms','idx','bdx'],
                'page' => 'snippets'
            ];
        }

        // Navigation: Links
        if ($subdomainAuth->canManageNav() || $this->blogsAuth->canManageLinks($this->auth)) {
            $navigation[] = [
                'title' => __('Navigation'),
                'link' => $this->settings->URLS['URL_BACKEND'] . 'cms/navs/' . $subdomainPostLink,
                'icon' => 'link',
                'app' => 'cms',
                'page' => 'navs'
            ];
        }

        // Navigation: Blogs
        $blogNavigation = [];
        if ($this->blogsAuth->canManageEntries($this->auth) || ($this->blogsAuth->canManageSelf($this->auth))) {
            $blogNavigation []= [
                'title' => __('Blog Posts'),
                'link' => $this->settings->URLS['URL_BACKEND'] . 'blog/entries/',
                'icon' => 'page', 'app' => 'blog',
                'page' => 'entries'
            ];
        }
        if ($this->blogsAuth->canManageCategories($this->auth)) {
            $blogNavigation []= [
                'title' => __('Categories'),
                'link' => $this->settings->URLS['URL_BACKEND'] . 'blog/categories/',
                'icon' => 'groups',
                'app' => 'blog',
                'page' => 'categories'
            ];
        }
        if ($this->blogsAuth->canManageComments($this->auth) ||  ($this->blogsAuth->canManageSelf($this->auth))) {
            $blogNavigation[]= [
                'title' => __('Comments'),
                'link' => $this->settings->URLS['URL_BACKEND'] . 'blog/comments/',
                'icon' => 'comment',
                'app' => 'blog',
                'page' => 'comments'
            ];
        }
        if (!empty($blogNavigation)) {
            if (!empty($navigation)) {
                $navigation[] = ['type' => 'line'];
            }
            $navigation = array_merge($navigation, $blogNavigation);
        }

        // Navigation: Tools
        if ($subdomainAuth->canViewTools()) {
            if (!empty($navigation)) {
                $navigation[] = ['type' => 'line'];
            }
            $navigation[] = ['title' => __('Tools'), 'link' => $this->settings->URLS['URL_BACKEND'] . 'cms/tools/' . $subdomainPostLink, 'icon' => 'tools', 'app' => 'cms', 'page' => 'tools'];
            ;
        }

        // Set Navigation
        return $navigation;
    }

    /**
     * Load Add Links
     * @return array
     */
    protected function loadAddLinks()
    {

        // Check if content can be displayed
        $subdomain = $this->getCurrentSubdomain('canManagePages');
        $subdomainAuth = $subdomain ? $subdomain->getAuth() : $this->domainAuth;
        $subdomainPostLink = $subdomain ? $subdomain->getPostLink() : '';

        // Add Links: Pages
        $addLinks = [];
        if ($subdomainAuth->canManagePages()) {
             $addLinks []= [
                'title' => __('Page'),
                'link' => $this->settings->URLS['URL_BACKEND'] . 'cms/pages/add/' . $subdomainPostLink,
                'icon' => 'page'
             ];
        }
        // Add Links: Snippets
        if ($subdomainAuth->canManageSnippets()) {
            $addLinks []= [
                'title' => __('Snippet'),
                'link' => $this->settings->URLS['URL_BACKEND'] . 'cms/snippets/add/' . $subdomainPostLink,
                'icon' => 'snippet'
            ];
        }
        // Add Links: Links
        if ($subdomainAuth->canManagePages()) {
            $addLinks []= [
                'title' => __('Link'),
                'link' => $this->settings->URLS['URL_BACKEND'] . 'cms/links/add/' . $subdomainPostLink,
                'icon' => 'link'
            ];
        }

        // Add Links: Blogs
        $blogAddLinks = [];
        if ($this->blogsAuth->canManageEntries($this->auth) || ($this->blogsAuth->canManageSelf($this->auth))) {
            $blogAddLinks []= [
                'title' => __('Post'),
                'link' => $this->settings->URLS['URL_BACKEND'] . 'blog/entries/add/' . ($this->blogsAuth->canManageEntries($this->auth) ? '' : $subdomainPostLink),
                'icon' => 'page'
            ];
        }
        if ($this->blogsAuth->canManageCategories($this->auth) ||  ($this->blogsAuth->canManageSelf($this->auth))) {
            $blogAddLinks []= [
                'title' => __('Category'),
                'link' => $this->settings->URLS['URL_BACKEND'] . 'blog/categories/add/' . ($this->blogsAuth->canManageCategories($this->auth) ? '' : $subdomainPostLink),
                'icon' => 'groups'
            ];
        }
        if (!empty($blogAddLinks)) {
            if (!empty($addLinks)) {
                $addLinks[] = ['type' => 'line'];
            }
            $addLinks = array_merge($addLinks, $blogAddLinks);
        }

        // Add Links: Tools
        $toolAddLinks = [];
        if ($subdomainAuth->canManageTestimonials($this->auth)) {
            $toolAddLinks []= [
                'title' => __('Testimonial'),
                'link' => $this->settings->URLS['URL_BACKEND'] . 'cms/tools/testimonials/?add'
                    . ($this->toolsAuth->canManageTestimonials($this->auth) ? '' : $subdomain->getPostLink(true)),
                'icon' => 'add'
            ];
        }
        if ($subdomainAuth->canManageRewrites($this->auth)) {
            $toolAddLinks []= [
                'title' => __('Redirect'),
                'link' => $this->settings->URLS['URL_BACKEND'] . 'cms/tools/rewrites/?add'
                    . ($this->toolsAuth->canManageRewrites($this->auth) ? '' : $subdomain->getPostLink(true)),
                'icon' => 'add'
            ];
        }
        if (!empty($toolAddLinks)) {
            if (!empty($addLinks)) {
                $addLinks[] = ['type' => 'line'];
            }
            $addLinks = array_merge($addLinks, $toolAddLinks);
        }

        // Set Add Links
        return $addLinks;
    }
}
