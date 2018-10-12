<?php

use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\History\UserInterface;

/**
 * History_User
 * @abstract
 */
abstract class History_User implements UserInterface
{

    /**
     * Type of User: ENUM('Agent', 'Associate', 'Lender', 'Lead')
     * @var string
     */
    protected $type;

    /**
     * User ID
     * @var integer
     */
    protected $user;

    /**
     * User Data
     * @var array
     */
    protected $data;

    /**
     * Database Connection
     * @var PDO
     */
    protected $db;

    /**
     * @var PDOStatement
     */
    protected $insertStmt;

    /**
     * History User: Agent
     * @var string
     */
    const TYPE_AGENT = 'Agent';

    /**
     * History User: Inside Sales Associate
     * @var string
     */
    const TYPE_ASSOCIATE = 'Associate';

    /**
     * History User: Lender
     * @var string
     */
    const TYPE_LENDER = 'Lender';

    /**
     * History User: Lead
     * @var string
     */
    const TYPE_LEAD = 'Lead';

    /**
     * __construct
     * @param int|null $user    User ID
     * @param PDO DBInterface      DB Connection (Optional, DBInterface)
     */
    public function __construct($user = null, DBInterface $db = null)
    {

        // Set User ID
        $this->user = $user;

        // Set Database Connection
        $this->db = is_null($db) ? DB::get('users') : $db;
    }

    /**
     * Change the user we're working with and reset user data
     * @param int $user
     */
    public function setUser($user)
    {
        $this->user = $user;
        $this->data = [];
        $this->id = null;
    }

    /**
     * Retrieve User Data from Database
     */
    abstract public function getUserRow();


    /**
     * Display Link to User Summary
     */
    abstract public function displayLink();

    /**
     * Set DB Connection
     * @param DBInterface $db
     */
    public function setDB(DBInterface $db)
    {
        $this->db = $db;
    }

    /**
     * Retrieve User ID
     * @return int $this->user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Retrieve User Type
     * @return string $this->type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get User Data by Key
     *
     * @param $key
     */
    public function getUserData($key = null)
    {

        /* Load User Data */
        if (empty($this->data)) {
            $this->data = $this->getUserRow();
        }

        /* Return User Data */
        if (!is_null($key)) {
            return $this->data[$key];
        } else {
            return $this->data;
        }
    }

    /**
     * Save Event User into Database
     *
     * @param History_Event $event
     * @throws Exception
     */
    public function save($event)
    {

        try {
            // Build INSERT Query and cache prepared statement
            $queryString = sprintf("INSERT INTO `%s` SET `type` = ?, `user` = ?, `event` = ?;", Settings::getInstance()->TABLES['HISTORY_USERS']);
            $queryParams = [$this->type, $this->user, $event->getID()];

            // Execute Query
            $this->insertStmt = $this->db->prepare($queryString);
            $this->insertStmt->execute($queryParams);

            // Set User ID
            $this->id = $this->db->lastInsertId();

            // Return User ID
            return $this->id;

        // Catch Exception
        } catch (Exception $e) {
            throw $e;
        }
    }
}
