<?php

/**
 * Permission Dictating if a team member is allowing other team members to view and edit their leads
 * @package Backend_Team_Permission
 */
class Backend_Team_Permission_Granting_ShareLeads extends Backend_Team_Permission
{

    protected $column = "share_leads";

    protected $title;

    protected $description;

    protected $primaryDescription;

    protected $values;

    protected $priority = 3;

    protected $agent_editable = true;

    protected $key = Backend_Team::GRANTING_KEY;


    public function __construct()
    {
        $this->title = __('Share Leads with Team');

        $this->description = __('If enabled, this agent will share its leads with other members of this team.');

        $this->primaryDescription = __('If enabled, this teams Primary Agent will share its leads with other agents in this team with the required permissions.');

        $this->values = [
        "full" => [
            'title' => __('Full Edit'),
            'value' => Backend_Team::PERM_SHARE_LEADS_FULL
        ],
        "edit" => [
            'title' => __('Partial Edit'),
            'value' => Backend_Team::PERM_SHARE_LEADS_EDIT
        ],
        "view" => [
            'title' => __('View'),
            'value' => Backend_Team::PERM_SHARE_LEADS_VIEW
        ],
        "false" => [
            'title' => __('No'),
            'value' => null
        ]
    ];

    }
}
