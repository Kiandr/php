<?php

/**
 * Permission Dictating if a team member can view and edit agents in this team
 * @package Backend_Team_Permission_Granted_AccessLeads
 */
class Backend_Team_Permission_Granted_AccessLeads extends Backend_Team_Permission
{

    protected $column = "access_leads";

    protected $title;

    protected $description;

    protected $values;

    protected $priority = 4;

    protected $key = Backend_Team::GRANTED_KEY;

    public function __construct()
    {
        $this->title = __('Can Access Team Leads');

        $this->description = __('If enabled, this agent will have access to this teams leads.  View allows agents to see all lead data, edit allows agents to edit non-essential fields and full access allows the agent to treat the lead as their own.');

        $this->values = [
        "full" => [
            'title' => __('Full Edit'),
            'value' => Backend_Team::PERM_ACCESS_LEADS_FULL
        ],
        "edit" => [
            'title' => __('Partial Edit'),
            'value' => Backend_Team::PERM_ACCESS_LEADS_EDIT
        ],
        "view" => [
            'title' => __('View'),
            'value' => Backend_Team::PERM_ACCESS_LEADS_VIEW
        ],
        "false" => [
            'title' => __('No'),
            'value' => null
        ]
    ];

    }
}
