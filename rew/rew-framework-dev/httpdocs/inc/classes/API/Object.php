<?php

/**
 * API_Object
 *
 */
class API_Object
{

    /**
     * Database connection
     * @var DB
     */
    protected $_db;

    /**
     * Record from database
     * @var array
     */
    protected $_row = array();

    /**
     * Create a new API Object
     * @param DB $db
     * @param array $row
     */
    public function __construct($db, $row)
    {
        $this->_db = $db;
        $this->_row = $row;
        return $this;
    }

    /**
     * Get the object's data
     * @return array
     */
    public function getData()
    {
        return $this->_row;
    }
}
