<?php

namespace REW\Backend\Auth\Interfaces;

/**
 * Interface SubdomainAuth
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Chelsea Urquhart <urquhart.chelsea@realestatewebmasters.com>
 */
interface SubdomainAuthInterface
{

    /**
     * Can Create Team Subdomains
     *
     * @return bool
     */
    public function canCreateSubdomains();

    /**
     * Can Manage All Team Subdomains
     *
     * @return bool
     */
    public function canManageSubdomains();

    /**
     * Can Manage Team Subdomains that this authuser belongs to
     *
     * @return bool
     */
    public function canManageOwnSubdomain();

    /**
     * Can Manage Homepage
     *
     * @return bool
     */
    public function canManageHomepage();

    /**
     * Can View Pages
     *
     * @return bool
     */
    public function canViewPages();

    /**
     * Can Manage Agent CMS Snippets
     *
     * @return bool
     */
    public function canManageSnippets();

    /**
     * Can Manage Team IDX Snippets
     *
     * @return bool
     */
    public function canManageIDXSnippets();

    /**
     * Can Manage BDX Snippets
     *
     * @return bool
     */
    public function canManageBDXSnippets();

    /**
     * Can Manage Navigation
     *
     * @return bool
     */
    public function canManageNav();

    /**
     * Can Delete CMS Pages
     *
     * @return bool
     */
    public function canDeletePages();

    /**
     * Can Manage CMS Pages
     *
     * @return bool
     */
    public function canManagePages();

    /**
     * Can Delete CMS Snippets
     *
     * @return bool
     */
    public function canDeleteSnippets();

    /**
     * Can View Tools
     *
     * @return bool
     */
    public function canViewTools();

    /**
     * Can View CMS Pages
     *
     * @return bool
     */
    public function canManageBackup();

    /**
     * Can Conversion Tracking
     *
     * @return bool
     */
    public function canManageConversionTracking();

    /**
     * Can Manage Team Subdomain Radio Landing Pages
     *
     * @return bool
     */
    public function canManageRadioLandingPage();

    /**
     * Can Manage Rewrite
     *
     * @return bool
     */
    public function canManageRewrites();

    /**
     * Can Manage Slideshows
     *
     * @return bool
     */
    public function canManageSlideshow();

    /**
     * Can Manage Testimonials
     *
     * @return bool
     */
    public function canManageTestimonials();

    /**
     * Can Manage Tracking Codes
     *
     * @return bool
     */
    public function canManageTracking();

    /**
     * Can Manage Communities
     *
     * @return bool
     */
    public function canManageCommunities();

    /**
     * Can Manage All Developments
     *
     * @return bool
     */
    public function canManageDevelopments();

    /**
     * Can Manage Own Developments
     *
     * @return bool
     */
    public function canManageOwnDevelopments();

    /**
     * Can Delete Developments
     *
     * @return bool
     */
    public function canDeleteDevelopments();

    /**
     * Can Manage Directory Snippets
     *
     * @return bool
     */
    public function canManageDirectorySnippets();
}
