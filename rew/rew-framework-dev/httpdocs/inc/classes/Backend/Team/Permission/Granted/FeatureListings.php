<?php

/**
 * Permission Dictating if a team member is featuring other team members on their listings
 * @package Backend_Team_Permission
 */
class Backend_Team_Permission_Granted_FeatureListings extends Backend_Team_Permission
{

    protected $column = "feature_listings";

    protected $title;

    protected $description;

    protected $values;

    protected $priority = 2;

    protected $key = Backend_Team::GRANTED_KEY;

    public function __construct()
    {
        $this->title = __('Featured on Team Listings');

        $this->description = __('If enabled, this agent will share access to their leads with this team.  View allows agents to see this agents leads, edit allows agents to edit this agents leads non-essential fields and full access allows the team members to treat this agents leads as their own.');

        $this->values = [
        "true" => [
            "title" => __('Yes'),
            "value" => Backend_Team::PERM_FEATURE_LISTINGS
        ],
        "false" => [
            "title" => __('No'),
            "value" => null
        ],
    ];

    }
}
