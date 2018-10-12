<?php

/**
 * OAuth_Calendar_Event is an abstract class used for building the third party event object
 */

abstract class OAuth_Event
{
    
    /**
     * Start Time
     * @var string
     */
    public $start;
    
    /**
     * Start End
     * @var string
     */
    public $end;
    
    /**
     * Title
     * @var string
     */
    public $title;
    
    /**
     * Event ID
     * @var string
     */
    public $type;
    
    /**
     * Description
     * @var string
     */
    public $description;
    
    /**
     * All Day Event
     * @var boolean
     */
    public $all_day_event;
    
    /**
     * Event ID
     * @var string
     */
    public $event_id;
}
