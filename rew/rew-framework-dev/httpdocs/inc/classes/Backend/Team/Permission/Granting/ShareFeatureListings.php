<?php

/**
 * Permission Dictating if a team member is featuring other team members on their listings
 * @package Backend_Team_Permission
 */
class Backend_Team_Permission_Granting_ShareFeatureListings extends Backend_Team_Permission
{

    protected $column = "share_feature_listings";

    protected $title;

    protected $description;

    protected $primaryDescription;

    protected $values;

    protected $priority = 1;

    protected $agent_editable = true;

    protected $key = Backend_Team::GRANTING_KEY;

    public function __construct()
    {
        $this->title = __('Share Listings With Team');

        $this->description = __('If enabled, other agents in this team will be able to be featured on this agents IDX & CMS listings.');

        $this->primaryDescription = __('If enabled, the Primary Agents IDX & CMS listings will also feature other agents in this team with the requried permissions.');

        $this->values = [
        "true" => [
            "title" => __('Yes'),
            "value" => Backend_Team::PERM_SHARE_FEATURE_LISTINGS
        ],
        "false" => [
            "title" => __('No'),
            "value" => null
        ],
    ];


    }
}
