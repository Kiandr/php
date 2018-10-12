<?php

use REW\Core\Interfaces\Backend\CMSInterface;

require_once __DIR__ . '/../../../backend/inc/php/functions/funcs.CMS.php';

class Backend_CMS implements CMSInterface
{
    /**
     * Get list of snippets to exclude from display (require installed add-ons)
     *
     * @return array
     */
    public function getExcludedSnippets()
    {
        return getExcludedSnippets();
    }

    /**
     * Check Table to Make sure Link is Unique
     *
     * @param string $link
     * @param string $table
     * @param string $key - table column that needs to be unique
     * @return string - unique key
     */
    public function uniqueLink($link, $table, $key = 'link')
    {
        return uniqueLink($link, $table, $key);
    }

    /**
     * Create Agent CMS Site
     * @param array $agent
     * @return array
     */
    public function agentSite($agent)
    {
        $errors = array();
        agent_site($agent, $errors);
        return $errors;
    }

    /**
     * Create Team CMS Site
     * @param array $team
     * @return array
     */
    function teamSite($team)
    {
        $errors = array();
        team_site($team, $errors);
        return $errors;
    }
}
