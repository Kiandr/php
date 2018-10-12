<?php

namespace REW\Backend\CMS\Interfaces\SubdomainFactory;

use REW\Backend\Auth\Interfaces\SubdomainAuthInterface;
use REW\Backend\CMS\Interfaces\SubdomainInterface;

interface SubdomainSettingsInterface
{

    /**
     * Gets the Subdomains Id
     * @return int
     */
    public function getId();

    /**
     * Gets the Subdomains Type
     * @return string
     */
    public function getType();

    /**
     * Gets the Subdomains Link
     * @return string
     */
    public function getLink();

    /**
     * Gets the Subdomain Title
     * @return string
     */
    public function getTitle();

    /**
     * Configures this subdomain from the current request and returns true if possible. If not, returns false.
     *
     * @param string|null $checkPermission The permission check to run on each subdomain
     * This should be a valid method in SubdomainAuthInterface
     * @return bool
     */
    public function configureFromRequest($checkPermission = null);

    /**
     * Configures this subdomain from the logged in agent if they have a default subdomain. Returns true if this is
     * the case, false if not
     * @param string|null $checkPermission The permission check to run on each subdomain
     * This should be a valid method in SubdomainAuthInterface
     * @return bool
     */
    public function configureDefault($checkPermission = null);

    /**
     * Builds a subdomain object from an array.
     * @param array $context
     * @return SubdomainInterface
     * @throws \Exception if no subdomain could be configured
     */
    public function configureFromArray(array $context);

    /**
     * @return SubdomainAuthInterface
     */
    public function getAuth();

    /**
     * Get list of available subdomains
     * @return SubdomainInterface[]
     */
    public function getSubdomainList();

    /**
     * Validates that this is adequately configured
     * @throws \Exception if the configuration parameters were invalid
     */
    public function validate();

    /**
     * Get the default feed for this subdomain
     * @return string
     */
    public function getDefaultFeed();
}
