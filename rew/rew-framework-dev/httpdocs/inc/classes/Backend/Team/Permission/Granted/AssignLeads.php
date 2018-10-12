<?php

/**
 * Permission Dictating if this Agent will be assigned team subdomain agents
 * @package Backend_Team_Permission
 */
class Backend_Team_Permission_Granted_AssignLeads extends Backend_Team_Permission
{

    protected $column = "assign_leads";

    protected $title;

    protected $description;

    protected $values;

    protected $priority = 6;

    protected $key = Backend_Team::GRANTED_KEY;

    public function __construct()
    {
        $this->title = __('Team Subdomain Auto-Assignment');

        $this->description = __('If enabled, this agent will be added to the rotation of agents receiving leads from this teams subdomain.');

        $this->values = [
        "true" => [
            "title" => __('Yes'),
            "value" => Backend_Team::PERM_ASSIGN
        ],
        "false" => [
            "title" => __('No'),
            "value" => null
        ],
    ];

    }
}
