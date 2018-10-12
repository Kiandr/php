<?php

use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\History\EventInterface;

/**
 *
 * History_Event is an abstract class used for event tracking
 *
 * @package History
 *
 */
abstract class History_Event implements EventInterface
{

    /**
     * Event DB
     * @var DB
     */
    protected $db;

    /**
     * Event ID
     * @var int
     */
    protected $id;

    /**
     * Event UNIX Timestamp
     * @var int
     */
    protected $timestamp;

    /**
     * Event Type: ENUM('Action', 'Create', 'Update', 'Delete', 'Email', 'Phone')
     * @var string
     */
    protected $type;

    /**
     * Event Sub-Type
     * @var string
     */
    protected $subtype;

    /**
     * Event Data
     * @var array
     */
    protected $data = array();

    /**
     * Nnormalized data id
     * @var int
     */
    protected $norm_id;

    /**
     * Event Users
     *  - Collection of History_User Objects
     *  - Can be Empty for Automated/System Event
     * @var array
     */
    protected $users = array();

    /**
     * __construct
     * @param $data
     */
    public function __construct($data = array(), $users = array(), $db = null)
    {
        if ($db === null) {
            $db = Container::getInstance()->get(DBFactory::class)->get('users');
        }

        // Event Database
        $this->db = $db;

        /* Event Data */
        $this->setData($data);

        /* Event Users */
        $this->setUsers($users);

        /* Set Event Timestamp */
        $this->setTimestamp();

        // Event Type
        $parentClass = get_class($this);
        do {
            $className = $parentClass;
            $parentClass =  get_parent_class($className);
        } while ($parentClass !== __CLASS__);

        // Event Type & Subtype
        $this->type = str_replace('History_Event_', '', $className);
        $this->subtype = str_replace($className . '_', '', get_class($this));
    }

    /**
     * Sets data for this event
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Sets the user for this event
     * @param $users
     */
    public function setUsers($users)
    {
        $this->users = array();
        foreach ($users as $user) {
            if ($user instanceof History_User) {
                $this->users[] = $user;
            }
        }
    }

    /**
     * Sets the event timestamp
     */
    public function setTimestamp()
    {
        $this->timestamp = time();
    }

    /**
     * getMessage
     * @param array $options
     */
    abstract public function getMessage(array $options = array());

    /**
     * getData
     * @param $key
     */
    public function getData($key = null)
    {
        if (!is_null($key)) {
            return $this->data[$key];
        } else {
            return $this->data;
        }
    }

    /**
     * Get the event's redundant data
     * @return array|NULL
     */
    protected function getNormalDataToSave()
    {
        return null;
    }

    /**
     * getID
     *  - return event's ID
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * getTimestamp
     *  - return event's unix timestamp
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * getType
     *  - return event's type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * getSubtype
     *  - return event's sub-type
     */
    public function getSubtype()
    {
        return $this->subtype;
    }

    /**
     * Get Event Users
     * @return History_User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Create History Event(s) from Database using Event ID
     *
     * @param mixed $event - ID or array of IDs for History_Event
     * @param PDO $db - DB Connection (Optional, PDO)
     * @return History_Event|History_Event_Editor
     */
    public static function load($id, PDO $db = null)
    {

        // Event Collection
        $events = array();

        // Events to Select
        $select = is_array($id) ? explode("', '", $id) : $id;

        // Get DB Connection
        $db = is_null($db) ? DB::get('users') : $db;

        // Select History Events from Database
        $history = $db->fetchAll("SELECT `id`, `type`, `subtype`, `timestamp` FROM `" . Settings::getInstance()->TABLES['HISTORY_EVENTS'] . "` WHERE `id` IN ('" . $select . "') ORDER BY `timestamp` DESC;");
        foreach ($history as $row) {
            // History_Event Class
            $event = 'History_Event_' . $row['type'] . '_' . $row['subtype'];

            // Get Event Data
            $data = $db->fetch("SELECT `data`, `norm_id` FROM `" . Settings::getInstance()->TABLES['HISTORY_DATA'] . "` WHERE `event` = '" . $row['id'] . "';");
            $norm_id = $data['norm_id'];
            $data = Format::unserialize($data['data']);

            // Event Users
            $users = array();

            // Load Event Users
            $event_users = $db->fetchAll("SELECT `type`, `user` FROM `" . Settings::getInstance()->TABLES['HISTORY_USERS'] . "` WHERE `event` = '" . $row['id'] . "';");
            foreach ($event_users as $usr) {
                $user = 'History_User_' . $usr['type'];
                $user = new $user ($usr['user'], $db);
                $users[] = $user;
            }

            // Create Event Object
            $event = new $event ($data, $users, $db);

            // Set Event ID
            $event->id = $row['id'];

            // Set Nomralized Data ID
            $event->norm_id = $norm_id;

            // Set Event Timestamp
            $event->timestamp = strtotime($row['timestamp']);

            // Add to Collection
            $events[] = $event;
        }

        // Return Single Event
        if (!is_array($id)) {
            return $events[0];

        // Return Events Array
        } else {
            return $events;
        }
    }

    /**
     * Save History Event into Database
     *
     * @param DBInterface $db - DB Connection (Optional, DBInterface)
     * @return int
     */
    public function save(DBInterface $db = null)
    {

        try {
            // Get DB Connection
            $db = is_null($db) ?$this->db : $db;

            // Start Transaction
            $db->query("START TRANSACTION;");

            // Build INSERT Query
            $query = "INSERT INTO `" . Settings::getInstance()->TABLES['HISTORY_EVENTS'] . "` SET "
                . "`type`      = " . $db->quote($this->type) . ", "
                . "`subtype`   = " . $db->quote($this->subtype) . ", "
                . "`ip`        = INET_ATON(" . $db->quote($_SERVER['REMOTE_ADDR']) . "), "
                . "`timestamp` = NOW();";

            // Execute Query
            $db->query($query);

            // Get Event ID
            $this->id = $db->lastInsertId();

            // Serialize Event Data
            $this->norm_id = $this->saveNormalData();
            $data = serialize($this->getData());

            // Store Event Data
            $save = $db->prepare("INSERT INTO `" . Settings::getInstance()->TABLES['HISTORY_DATA'] . "` SET `event` = :event, `data` = :data, `norm_id` = :norm_id;");
            $save->execute(array('event' => $this->id, 'data' => $data, 'norm_id' => $this->norm_id));

            // Save Event Users
            if (!empty($this->users)) {
                foreach ($this->users as $user) {
                    if (!$user instanceof History_User) {
                        continue;
                    }
                    $user->setDB($db);
                    $user->save($this);
                }
            }

            // Commit Transaction
            $db->query("COMMIT;");

            // Action Plan task completion hook
            if (Settings::getInstance()->MODULES['REW_ACTION_PLANS']) {
                // Task shortcut used - user intends to complete a task - check if event types match
                if (!empty($_SESSION['task_shortcut'])) {
                    $success = array();
                    $errors  = array();

                    // Require Authuser and Lead
                    $authuser = Auth::get();
                    $lead = $db->fetch(
                        "SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `users` WHERE `id` = :user_id;",
                        array('user_id' => $_SESSION['task_shortcut']['user'])
                    );

                    // Require Authuser and lead
                    if (!empty($authuser) && !empty($lead)) {
                        $event_type  = $this->type . '_' . $this->subtype;
                        $task_events = $_SESSION['task_shortcut']['events'];

                        // Check if event types match
                        if (is_array($task_events) && in_array($event_type, $task_events)) {
                            // Match! Load task and complete
                            if ($task = Backend_Task::load($_SESSION['task_shortcut']['task'])) {
                                $performer = array('type' => $authuser->getType(), 'id' => $authuser->info('id'));

                                // Complete task and set notice
                                if ($task->resolve($lead['id'], $performer, 'Completed')) {
                                    $success[] = 'Completed Task ' . $task->info('name') . ' for ' . $lead['name'] . '!';
                                    $authuser->setNotices($success, $errors);
                                }
                            }
                        }
                    }

                    // Unset shortcut
                    unset($_SESSION['task_shortcut']);
                }
            }

            // Return Event ID
            return $this->id;

        // Database Error
        } catch (Exception $e) {
            // Rollback Transaction
            $db->query("ROLLBACK;");

            // Pass the exception back up the stack
            throw $e;
        }

        // Return False
        return false;
    }

    /**
     * Save normalized data to database
     * @return NULL|int
     */
    protected function saveNormalData()
    {

        // Require data
        $data = $this->getNormalDataToSave();
        if (is_null($data)) {
            return null;
        }

        // Get DB Connection
        $db = is_null($db) ?$this->db : $db;

        // Check if a record for this data already exists
        $hash = md5($data);
        $data_stmt = $db->prepare("SELECT `id` FROM `history_data_normal` WHERE `hash` = UNHEX(:hash) LIMIT 1;");
        $data_stmt->execute(array('hash' => $hash));
        $data_id = $data_stmt->fetchColumn(0);
        if (empty($data_id)) {
            // Add new record to database
            $data_stmt = $db->prepare("INSERT INTO `history_data_normal` SET `data` = :data, `hash` = UNHEX(:hash);");
            $data_stmt->execute(array('data' => $data, 'hash' => $hash));
            $data_id = $db->lastInsertId();
        }

        // Return Norm Data ID
        return $data_id;
    }

    /**
     * Load normalized data from database
     * @return NULL|string
     */
    protected function loadNormalData()
    {

        $id = $this->norm_id;

        if (is_null($id)) {
            return null;
        }

        // Get DB Connection
        $db = is_null($db) ?$this->db : $db;

        // Check if a record for this data already exists
        $data_stmt = $db->prepare("SELECT `data` FROM `history_data_normal` WHERE `id` = :id LIMIT 1;");
        $data_stmt->execute(array('id' => $id));
        $data = $data_stmt->fetchColumn(0);

        // Return Norm Data
        return $data;
    }

    /**
     * Check If History Event Can Be Edited
     * @param AuthInterface $authuser
     * @return boolean
     */
    public function canEdit(AuthInterface $authuser = null)
    {
        // Must Implement 'History_IEditable' & Have Editable Fields
        $can_edit = ($this instanceof History_IEditable && $this->getEditable());
        if (empty($can_edit)) {
            return false;
        }
        // Check AuthUser
        if (!empty($authuser)) {
            $can_edit = false;
            foreach ($this->getUsers() as $user) {
                if ($authuser->isSuperAdmin()
                    || ($authuser->info('id') === $user->getUser() && (
                        ($authuser->isAgent() && $user->getType() === $user::TYPE_AGENT)
                        || ($authuser->isLender() && $user->getType() === $user::TYPE_LENDER)
                        || ($authuser->isAssociate() && $user->getType() === $user::TYPE_ASSOCIATE)
                    ))
                ) {
                    $can_edit = true;
                    break;
                }
            }
        }
        // Return Check
        return $can_edit;
    }

    /**
     * Save Edits to History Event
     * @param array $data
     * @param array $errors
     * @param DBInterface $db DB Connection
     * @return boolean True on success, False on error
     */
    public function edit(array $data, &$errors = array(), DBInterface $db = null)
    {

        // Must Be Able to Edit
        if (!$this->canEdit()) {
            $errors[] = 'You are unable to edit this event.';
            return false;
        }

        // Get DB Connection
        $db = is_null($db) ? $this->db : $db;

        // Get Edits
        $edits = array();
        foreach ($this->getEditable() as $field) {
            if (!empty($data[$field])) {
                $edits[$field] = $data[$field];
            }
        }

        // No Edits to Make
        if (empty($edits)) {
            $errors[] = 'You must complete the form to save your changes.';
            return false;
        }

        // Merge Edits
        $data = serialize(array_merge($this->getData(), $edits));

        // Build UPDATE Query
        $query = "UPDATE `" . Settings::getInstance()->TABLES['HISTORY_DATA'] . "` SET `data` = " . $db->quote($data) . " WHERE `event` = " . $db->quote($this->id) . ";";

        // Execute Query
        if ($db->query($query)) {
            // Update Event Data
            foreach ($edits as $field => $edit) {
                $this->data[$field] =  $edit;
            }

            // Success
            return true;

        // Query Error
        } else {
            $errors[] = 'An error has occurred. Your changes could not be saved.';
            return false;
        }
    }
}
