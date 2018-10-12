<?php

/**
 * Backend_Task is the base task class for interacting with task data. Abstract class extended by concrete task types.
 *
 * @package Backend
 */
abstract class Backend_Task
{

    /**
     * Action Plan ID
     * @var int $id
     */
    protected $id;

    /**
     * Database Object
     * @var DB $db
     */
    protected $db;

    /**
     * Settings object
     * @var Settings $settings
     */
    protected $settings;

    /**
     * Action Plan
     * @var array $row
     */
    protected $row = array();

    /**
     * Create a Task Object
     *
     * <code>
     * <?php
     *
     * $db = DB::get();
     *
     * $result = $db->fetch("SELECT * FROM `tasks` WHERE `parent_id` = " . $this->db->quote($this->getId()) . " LIMIT 1;");
     *
     * $task = Backend_Task::create($result);
     *
     * ?>
     * </code>
     *
     * @param array $row Data Row
     */
    protected function __construct($row = array())
    {

        // CMS DB
        $this->db = DB::get('cms');

        // Settings
        $this->settings = Settings::getInstance();

        // Task Row
        $this->row = $row;

        // Task ID - existing task
        if (isset($row['id'])) {
            // Set Id
            $this->id = $row['id'];

            // Load task type content (ex. document, text message, etc.)
            $this->loadTaskContent();
        }
    }

    /**
     * Saves/Updates Task in DB
     *
     * @param DB $db
     * @throws Exception_ValidationError
     */
    public function save(DB $db = null)
    {

        // Lead Database
        $db = is_null($db) ? $this->db : $db;

        // Task Row
        $row = $this->getRow();

        // Require integer for due and expire offsets
        $row['offset'] = intval($row['offset']);
        $row['expire'] = intval($row['expire']);

        // Validate Name
        if (empty($row['name'])) {
            throw new Exception_ValidationError('Task Name Required');
        }

        // Validate Performer
        if (!in_array($row['performer'], array('Agent', 'Lender', 'Associate'))) {
            throw new Exception_ValidationError('Invalid Task Performer');
        }

        // Validate Offset
        if ($row['offset'] < 0) {
            throw new Exception_ValidationError('Invalid Due Offset Value');
        }

        // Validate Expire Time
        if ($row['expire'] < 0) {
            throw new Exception_ValidationError('Invalid Expire Offset Value');
        }

        // Validate Due Time
        if (empty($row['time'])) {
            throw new Exception_ValidationError('Invalid Due Time');
        }

        $row['automated'] = !empty($row['automated']) ? $row['automated'] : 'N';
        $row['info']      = !empty($row['info'])      ? $row['info'] : '';
        $row['parent_id'] = !empty($row['parent_id']) ? $row['parent_id'] : null;

        $params = array(
            'name'      => $row['name'],
            'performer' => $row['performer'],
            'automated' => $row['automated'],
            'info'      => $row['info'],
            'offset'    => $row['offset'],
            'expire'    => $row['expire'],
            'time'      => $row['time'],
            'parent_id' => $row['parent_id']
        );

        // Existing Task
        if (!empty($this->id)) {
            // Update Existing Row
            $query = $this->db->prepare("UPDATE `tasks` SET "
                . "`name`              = :name, "
                . "`performer`         = :performer, "
                . "`automated`         = :automated, "
                . "`info`              = :info, "
                . "`offset`            = :offset, "
                . "`expire`            = :expire, "
                . "`time`              = :time, "
                . "`parent_id`         = :parent_id, "
                . "`timestamp_updated` = NOW() "
                . "WHERE id            = :task_id;");

            $params['task_id'] = $this->id;

            $query->execute($params);
        } else {
            // Insert new Task
            $query = $this->db->prepare("INSERT INTO `tasks` SET "
                . "`actionplan_id`     = :actionplan_id, "
                . "`name`              = :name, "
                . "`type`              = :type, "
                . "`performer`         = :performer, "
                . "`automated`         = :automated, "
                . "`info`              = :info, "
                . "`offset`            = :offset, "
                . "`expire`            = :expire, "
                . "`time`              = :time, "
                . "`parent_id`         = :parent_id, "
                . "`timestamp_updated` = NOW(), "
                . "`timestamp_created` = NOW();");

            $params['actionplan_id'] = $row['actionplan_id'];
            $params['type'] = $row['type'];

            $query->execute($params);

            $this->id = $this->db->lastInsertId();
        }

        // Updates associated tables for task specific data (also stored in $row)
        $this->saveTaskContent();

        // Set the updated timestamp of the plan this task is associated with
        $this->db->query("UPDATE `action_plans` SET `timestamp_updated` = NOW() WHERE `id` = " . $db->quote($row['actionplan_id']) . ";");

        return true;
    }

    /**
     * Instantiate a task with $row of task data
     *
     * @param array $row
     * @return Backend_Task
     */
    public static function create($row = array())
    {

        // Format type to class name
        $type_class = str_replace(' ', '', $row['type']);
        $class = (__CLASS__) . '_' . $type_class;

        if (class_exists($class)) {
            $task = new $class($row);
            return $task;
        } else {
            throw new Exception_ValidationError('Invalid Task Type');
        }
    }

    /**
     * Load row of task data from DB and instantiate task
     *
     * @param int $id
     * @return Backend_Task
     */
    public static function load($id)
    {

        $db = DB::get('cms');

        // load the basic task row - concrete task type class will load additional data
        if ($row = $db->fetch("SELECT * FROM `tasks` WHERE `id` = :task_id;", array('task_id' => $id))) {
            return self::create($row);
        } else {
            return false;
        }
    }

    /**
     * Snooze this task for the specified user, by amount in hours
     *
     * @param int $user_id
     * @param array $performer
     * @param int $hours
     * @param string $note
     * @param string $mode ('minutes' || 'hours')
     */
    public function snooze($user_id, $performer, $duration, $note = '', $mode = 'hours')
    {
        // Invalid mode
        if (!in_array($mode, ['minutes','hours','days','weeks'])) {
            return false;
        }

        // Add timestamp to note
        $note = 'Snoozed ' . $duration . ' ' . $mode . (!empty($note) ? ': ' . $note : '');

        // Check if the due date has passed. If yes: snooze should be based on the current time + snooze duration
        $check = $this->db->fetch("SELECT "
            . " TIMESTAMPDIFF(SECOND, `timestamp_due`, `timestamp_expire`) AS `seconds_diff`, "
            . " `timestamp_due`, "
            . " `timestamp_expire` "
            . " FROM `users_tasks` "
            . " WHERE `task_id` = :task_id "
            . " AND `user_id` = :user_id "
            . " AND `timestamp_due` <= NOW() "
            . " LIMIT 1 "
            . ";"
        , [
            'task_id' => $this->getId(),
            'user_id' => $user_id
        ]);

        switch ($mode) {
            case 'minutes' :
                $interval = 'MINUTE';
                break;
            case 'hours' :
                $interval = 'HOUR';
                break;
            case 'days' :
                $interval = 'DAY';
                break;
            case 'weeks' :
                $duration = $duration * 7;
                $interval = 'DAY';
                break;
        }

        // Update the task due and expire timestamps
        $query = $this->db->prepare("UPDATE `users_tasks` SET "
            . (!empty($check)
                ? sprintf(
                    ' `timestamp_due` = DATE_ADD(NOW(), INTERVAL :duration %1$s), '
                    . ' `timestamp_expire` = DATE_ADD(DATE_ADD(NOW(), INTERVAL :duration %1$s), INTERVAL :seconds_extra SECOND) ',
                    $interval
                ) : sprintf(
                    ' `timestamp_due` = DATE_ADD(`timestamp_due`, INTERVAL :duration %1$s), '
                    . ' `timestamp_expire` = DATE_ADD(`timestamp_expire`, INTERVAL :duration %1$s) ',
                    $interval
                )
            )
            . " WHERE `task_id` = :task_id "
            . " AND `user_id` = :user_id "
            . ";");
        $params = [
            'duration' => $duration,
            'task_id' => $this->getId(),
            'user_id' => $user_id
        ];
        if (!empty($check)) {
            $params['seconds_extra'] = ($check['seconds_diff'] > 0) ? $check['seconds_diff'] : 0;
        }

        if ($query->execute($params)) {
            // Add note
            $this->addNote($user_id, $note);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Used to resolve (complete/dismiss/expire) a task for a specified user.
     *
     * @param int $user_id The user the task is being completed for
     * @param array $performer The backend user that resolved the task. Format: array('id' => 1, 'type' => 'Agent')
     * @param string $status The resolved status. Values: 'Completed', 'Dismissed', 'Expired'
     * @param string $note A note provided by the backend user when resolving the task.
     * @param boolean $followup_dismiss If dismissing a task, set to true to also dismiss all followup tasks.
     * @return boolean Returns true on success false on failure.
     */
    public function resolve($user_id, $performer, $status, $note = '', $followup_dismiss = false)
    {

        // Validate status
        if (!in_array($status, array('Completed', 'Dismissed', 'Expired'))) {
            return false;
        }

        // Build note for resolved task to be appended to existing notes
        $note  = $status . (!empty($note) ? ': ' . $note : '');

        // Set task resolved and status to complete
        $query = $this->db->prepare("UPDATE `users_tasks` SET "
            . " `performer`          = :performer, "
            . " `performer_id`       = :performer_id, "
            . " `status`             = :status, "
            . " `timestamp_resolved` = NOW() "
            . " WHERE `task_id` = :task_id AND `user_id` = :user_id "
            . ";");
        $params = array(
            'task_id'      => $this->getId(),
            'user_id'      => $user_id,
            'status'       => $status,
            'performer'    => $performer['type'],
            'performer_id' => $performer['id']
        );

        if ($query->execute($params)) {
            // Add note
            $this->addNote($user_id, $note);

            // Handle followup tasks if this task was expired or dismissed
            switch ($status) {
                case 'Expired':
                    // Expire followups TODO: maybe only do this for important/key tasks?
                    $tasks = $this->db->fetchAll("SELECT `id`, `parent_id` FROM `tasks` WHERE `parent_id` = '" . $this->getId() . "';");
                    foreach ($tasks as $task) {
                        $task = self::load($task['id']);
                        $task->resolve($user_id, $performer, 'Expired', 'Parent task expired.');
                    }
                    break;
                case 'Dismissed':
                    // Dismiss followups if followup_dismiss flag is true
                    if ($followup_dismiss) {
                        // Get all tasks in plan with this task and determine which are descendants and dismiss them as well
                        $tasks = $this->db->fetchAll("SELECT `id`, `parent_id` FROM `tasks` WHERE `parent_id` = '" . $this->getId() . "';");
                        foreach ($tasks as $task) {
                            $task = self::load($task['id']);
                            $task->resolve($user_id, $performer, 'Dismissed', 'Parent task dismissed.', true);
                        }
                    } else {
                        // Otherwise schedule the task children as usual
                        $this->scheduleFollowups($user_id);
                    }
                    break;
                case 'Completed':
                    $this->scheduleFollowups($user_id);
                    break;
            }

            // If last remaining task for this plan, set plan timestamp_completed
            $params = array('actionplan_id' => $this->info('actionplan_id'), 'user_id' => $user_id);
            $checkStatement = $this->db->prepare("SELECT COUNT(`ut`.`task_id`) AS `remaining`, `a`.`name` AS `plan_name` "
                . " FROM `users_tasks` `ut` "
                . " JOIN `action_plans` `a` ON `ut`.`actionplan_id` = `a`.`id` "
                . " WHERE `status` = 'Pending' "
                . " AND `actionplan_id` = :actionplan_id "
                . " AND `user_id` = :user_id"
                . ";");

            $checkStatement->bindValue(':actionplan_id', (int)$params['actionplan_id'], PDO::PARAM_INT);
            $checkStatement->bindValue(':user_id', (int)$params['user_id'], PDO::PARAM_INT);
            $checkStatement->execute();
            $check = $checkStatement->fetch();

            if ($check['remaining'] == 0) {
                $query = $this->db->prepare("UPDATE `users_action_plans` "
                    . " SET `timestamp_completed` = NOW() "
                    . " WHERE `actionplan_id` = :actionplan_id "
                    . " AND `user_id` = :user_id "
                    . ";");

                if ($query->execute($params)) {
                    // Log Event: Action Plan Completed
                    $event = new History_Event_Update_ActionPlanComplete(array(
                            'action_plan' => $check['plan_name']
                        ), array(
                            new History_User_Lead($user_id),
                            (!empty($performer) ? $this->performerToHistoryUser($performer) : null)
                        ));

                    // Save Event
                    $event->save($this->db);
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Add a note to the specified user's task.
     * @param int $user_id
     * @param string $note
     * @return boolean
     */
    public function addNote($user_id, $note)
    {

        // Get User Task ID
        $user_task = $this->db->fetch("SELECT `id` FROM `users_tasks` WHERE `task_id` = :task_id AND `user_id` = :user_id;", array(
            'task_id' => $this->getId(),
            'user_id' => $user_id
        ));

        // Create Note
        if (!empty($user_task['id'])) {
            $note_query = $this->db->prepare(
                "INSERT INTO `users_tasks_notes` SET "
                . " `user_id` = :user_id, "
                . " `user_task_id` = :user_task_id, "
                . " `note` = :note, "
                . " `timestamp` = NOW(); "
            );
            if ($note_query->execute(array(
                'user_id' => $user_id,
                'user_task_id' => $user_task['id'],
                'note' => htmlspecialchars($note)
            ))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Schedule Task for specified user
     * @param int $user_id
     */
    public function schedule($user_id, $allowed_days)
    {

        // Determine due and expire timestamps
        $offset = $this->info('offset');
        $time   = $this->info('time');
        $expire = $this->info('expire');

        // Tentative due time
        $due_time = new DateTime('+' . $offset . ' DAY');

        // Day interval for incrementing due time
        $day = new DateInterval('P1D');

        // Set the time to the due time specified on the task
        $time = explode(':', $time);
        $due_time->setTime(intval($time[0]), intval($time[1]), intval($time[2]));

        // If due time has already passed, set to next day
        if ($offset == 0 && ($due_time < (new DateTime('NOW')))) {
            $due_time->add($day);
        }

        // If days specified, check if adjustment needed
        if (!empty($allowed_days)) {
            if (!in_array($due_time->format('w'), $allowed_days)) {
                // Due day not in allowed list. Iterate through 7 days to find the next available.
                for ($d = 0; $d < 7; $d++) {
                    $due_time->add($day);
                    // Increase offset days for each day incremented
                    $offset++;
                    // Found an allowed day - break out
                    if (in_array($due_time->format('w'), $allowed_days)) {
                        break;
                    }
                }
            }
        }

        // Format to MYSQL timestamp
        $timestamp_due = $due_time->format('Y-m-d H:i:s');

        // Determine the expiry time
        $due_time->add(new DateInterval('P' . (!empty($expire) ? $expire : 1) . 'D'));
        $timestamp_expire = $due_time->format('Y-m-d H:i:s');

        // Duplication check
        $check = $this->db->fetch("SELECT `id` FROM `users_tasks` WHERE "
            . "`task_id` = :task_id AND "
            . "`user_id` = :user_id "
            . "LIMIT 1;",
            [
                'task_id' => $this->getId(),
                'user_id' => $user_id
            ]
        );

        if (empty($check)) {
            // Set Timezone to base follow up tasks on
            $timezone = $this->db->query(
                "SELECT `t`.`TZ` FROM `agents` `a` LEFT JOIN `timezones` `t` ON `a`.`timezone` = `t`.`id` WHERE `a`.`id` = 1 LIMIT 1;"
            );
            $timezone = $timezone->fetchColumn();
            if (!empty($timezone)) {
                // Set MySQL Timezone (PDO)
                $this->db->query("SET `time_zone` = '" . $timezone . "';");
            }
            
            $query = $this->db->prepare("INSERT INTO `users_tasks` SET "
                . "`actionplan_id`    = :actionplan_id, "
                . "`task_id`          = :task_id, "
                . "`user_id`          = :user_id, "
                . "`performer`        = :performer, "
                . "`name`             = :name, "
                . "`type`             = :type, "
                . "`status`           = 'Pending', "
                . "`timestamp_due`    = :timestamp_due, "
                . "`timestamp_expire` = :timestamp_expire, "
                . "`timestamp_scheduled` = NOW();");
            $query->execute(array(
                'actionplan_id' => $this->info('actionplan_id'),
                'task_id' => $this->getId(),
                'user_id' => $user_id,
                'performer' => $this->info('performer'),
                'name' => $this->info('name'),
                'type' => $this->info('type'),
                'timestamp_due' => $timestamp_due,
                'timestamp_expire' => $timestamp_expire
            ));

            // Set MySQL Timezone (PDO)
            $this->db->query("SET `time_zone` = '" . date_default_timezone_get() . "';");
        }
    }

    /**
     * Used by tasks to schedule their children tasks after being completed
     *
     * @param $user_id The ID of the user that the task was completed for
     */
    protected function scheduleFollowups($user_id)
    {

        // Get children of this task
        $tasks = $this->db->fetchAll(
            sprintf(
                "SELECT * FROM `tasks` WHERE `parent_id` = %s %s;",
                $this->db->quote($this->getId()),
                self::getDisabledTaskTypes()
            )
        );

        // Get allowed days from action plan info to adjust due time if necessary
        $allowed_days = $this->db->fetch("SELECT `day_adjust` FROM `action_plans` WHERE `id` = '" . $this->info('actionplan_id') . "';");
        $allowed_days = explode(',', $allowed_days['day_adjust']);

        foreach ($tasks as $task) {
            // Use Backend_Task
            $task = self::create($task);
            $task->schedule($user_id, $allowed_days);
        }
    }

    /**
     * Get disabled tasks types to exclude from creation
     *
     * @return string
     */
    public static function getDisabledTaskTypes()
    {
        $types = [''];
        if(Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO'] === false) {
            $types[] = 'Text';
        }
        return " AND `type` NOT IN ('" . implode('\',\'', $types) . "')";
    }

    /**
     * Get / Set Info
     *
     * @param string $info
     * @param mixed $value
     * @return void|mixed
     */
    public function info($info, $value = null)
    {
        // Get Information
        if (is_string($info) && is_null($value)) {
            return $this->row[$info];
        }
        // Set Information
        if (is_string($info) && !is_null($value)) {
            $this->row[$info] = $value;
        }
    }

    /**
     * Get ID
     *
     * @return int Task ID
     */
    public function getId()
    {
        return intval($this->id);
    }

    /**
     * Get Row
     *
     * @return array Task Row
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Get Task Type Icon
     *
     * @return string Icon Class
     */
    public static function getTypeIcon($type)
    {
        switch ($type) {
            case 'Call':
                return 'icon-phone';
                break;
            case 'Email':
                return 'icon-envelope-alt';
                break;
            case 'Search':
                return 'icon-search';
                break;
            case 'Text':
                return 'icon-mobile-phone';
                break;
            case 'Group':
                return 'icon-group';
                break;
            case 'Listing':
                return 'icon-home';
                break;
            case 'Custom':
            default:
                return 'icon-question';
                break;
        }
    }

    /**
     * Set row for task editing
     *
     * @param array $row
     */
    public function setRow($row = array())
    {
        if (!empty($row) && is_array($row)) {
            $this->row = $row;
        }
    }

    /**
     * @param array $performer
     * @return mixed History_User
     */
    public function performerToHistoryUser(array $performer)
    {
        if (isset($performer['type']) && (isset($performer['id']) && !empty($performer['id']))) {
            if ($performer['type'] === 'Agent' || $performer['type'] === 'Lender' || $performer['type'] === 'Associate') {
                $historyClass = 'History_User_' . $performer['type'];
                return new $historyClass($performer['id']);
            }
        }
    }

    /**
     * Loads additional task (type specific) data. Implemented by task subclasses.
     */
    abstract protected function loadTaskContent();

    /**
     * Saves additional task (type specific) data. Implemented by task subclasses.
     */
    abstract protected function saveTaskContent();

    /**
     * Some task types can be processed through the system. Each of these subclasses implements their own processAndResolve method.
     *
     * @param int $user_id The ID of the user for which to run this task.
     * @param bool $automated Detemines whether this task is being processed via an automated script
     * @param bool $e_output Determines whether errors will be echoed or suppressed
     */
    abstract public function processAndResolve($user_id, $automated = false, $e_output = false);

    /**
     * Populates $_POST with task (type specific) data, for preloading forms when using task shortcuts.
     */
    abstract public function postTaskContent();

    /**
     * Get the backend URL where this task can be completed for the specified user.
     *
     * @param int $user_id
     * @param bool $special Return special URL if the task can be completed via a module/feature (ex. REW Text, REW Dialer)
     */
    abstract public function getShortcutURL($user_id, $special);

    /**
     * Get history event type associated with this task type
     */
    abstract public function getEventTypes();
}
