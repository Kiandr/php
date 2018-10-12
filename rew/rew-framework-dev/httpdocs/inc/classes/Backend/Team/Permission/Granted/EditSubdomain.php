<?php

/**
 * Permission Disctating if this Agent Can Edit Team Subdomains
 * @package Backend_Team_Permission
 */
class Backend_Team_Permission_Granted_EditSubdomain extends Backend_Team_Permission
{

    protected $column = "edit_subdomain";

    protected $title;

    protected $description;

    protected $values;

    protected $priority = 5;

    protected $key = Backend_Team::GRANTED_KEY;

    public function __construct()
    {
        $this->title = __('Can Edit Team Subdomain');

        $this->description = __('If enabled, this agent will be able to edit pages on this teams subdomain.');

        $this->values = [
        "true" => [
            "title" => __('Yes'),
            "value" => Backend_Team::PERM_EDIT_SUBDOMAIN
        ],
        "false" => [
            "title" => __('No'),
            "value" => null
        ],
    ];

    }
}
