<?php

namespace REW\Backend\Navigation\Interfaces;

interface NavigationInterface
{

    /**
     * Is this Navigation Menu Enabled
     * @return bool
     */
    public function isEnabled();

    /**
     * Return link to menus landing page
     * @return string
     */
    public function getLandingLink();

    /**
     * Get Navigation Links
     * @return array
     */
    public function getNavLinks();

    /**
     * Get Add Links
     * @return array
     */
    public function getAddLinks();

    /**
     * @return string
     */
    public function getNavName();
}
