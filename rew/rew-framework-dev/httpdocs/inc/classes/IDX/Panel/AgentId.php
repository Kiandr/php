<?php

/**
 * Search by Agent ID
 * @package IDX_Panel
 */
class IDX_Panel_AgentId extends IDX_Panel_Type_Input
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Search by Agent ID';

    /**
     * Input Name
     * @var string
     */
    protected $inputName = 'agent_id';

    /**
     * Input Class
     * @var string
     */
    protected $inputClass = 'x12 autocomplete';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'ListingAgentID';
}
