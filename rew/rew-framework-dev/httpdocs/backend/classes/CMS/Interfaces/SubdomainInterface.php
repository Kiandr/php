<?php

namespace REW\Backend\CMS\Interfaces;

use REW\Backend\Auth\Interfaces\SubdomainAuthInterface;

interface SubdomainInterface
{

    /**
     * @var string Agent Subdomain Type
     */
    const AGENT_SUBDOMAIN_KEY = 'agent';

    /**
     * @var string Team Subdomain Type
     */
    const TEAM_SUBDOMAIN_KEY = 'team';

    /**
     * @var string Domain Type
     */
    const DOMAIN_KEY = 'domain';

    /**
     * @var array Allowed Subdomain Types
     * @deprecated See SubdomainFactoryInterface::getTypes()
     */
    const TYPES = [self::AGENT_SUBDOMAIN_KEY, self::TEAM_SUBDOMAIN_KEY, self::DOMAIN_KEY];

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getLink();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return array
     */
    public function getTypes();

    /**
     * Get Upload Type for Open Graph Images
     *
     * @return string
     */
    public function getOgQuery();

    /**
     * Get Upload Type for Open Graph Images
     *
     * @return string
     */
    public function getOgType();

    /**
     * Get SQL used to find owner
     *
     * @param bool $includeShared Should shared entities be returned?
     * @return string
     */
    public function getOwnerSql($includeShared = false);

    /**
     * Get SQL used to assign to owner
     *
     * @return string
     */
    public function getAssignSql();

    /**
     * Get SQL used to assign to owner
     *
     * @param bool $andHref Should the link use & instead of ? when adding to link
     *
     * @return string
     */
    public function getPostLink($andHref = false);

    /**
     * Get SQL used to assign to owner
     *
     * @return string
     */
    public function getInput();

    /**
     * Get this subdomains title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Is subdomain www or not
     *
     * @return string
     */
    public function isPrimary();

    /**
     * Is the currently edited subdomain
     *
     * @param SubdomainInterface $subdomain  Subdomain to compare against
     *
     * @return bool
     */
    public function compare(SubdomainInterface $subdomain);

    /**
     * Gets the Auth handler for this subdomain.
     *
     * @return SubdomainAuthInterface
     */
    public function getAuth();

    /**
     * Validates that our settings are adequately configured
     * @throws \Exception if this could not be adequately configured
     */
    public function validateSettings();

    /**
     * Get the default feed for this subdomain
     * @return string
     */
    public function getDefaultFeed();
}
