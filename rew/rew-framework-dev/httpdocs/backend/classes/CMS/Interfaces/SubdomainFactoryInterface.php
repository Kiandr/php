<?php

namespace REW\Backend\CMS\Interfaces;

use REW\Backend\CMS\Interfaces\SubdomainFactory\SubdomainSettingsInterface;

interface SubdomainFactoryInterface
{
    /**
     * Builds a subdomain object from the current request.
     * @param string|null $checkPermission The permission check to run on each subdomain
     * This should be a valid method in SubdomainAuthInterface
     * @return SubdomainInterface|bool
     */
    public function buildSubdomainFromRequest($checkPermission = null);

    /**
     * Gets the Team Subdomain from Id
     *
     * @param SubdomainSettingsInterface[] $settings
     *
     * @return SubdomainInterface[]
     */
    public function getSubdomainsFromSettings(array $settings);

    /**
     * Gets available subdomain types
     *
     * @return array
     */
    public function getTypes();

    /**
     * Get list of available subdomains
     * @param string|null $checkPermission The permission check to run on each subdomain
     * This should be a valid method in SubdomainAuthInterface
     * @return SubdomainInterface[]
     */
    public function getSubdomainList($checkPermission = null);

    /**
     * Builds a subdomain object from a snippet.
     * @param array $context
     * @return SubdomainInterface
     * @throws \Exception if no subdomain could be found
     */
    public function buildSubdomainFromArray(array $context);
}
