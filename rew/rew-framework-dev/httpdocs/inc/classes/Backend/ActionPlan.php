<?php

class Backend_ActionPlan implements ArrayAccess
{

    /**
     * Action Plan ID
     * @var int $id
     */
    protected $id;

    /**
     * Database Object
     * @var object $db
     */
    protected $db;

    /**
     * Action Plan
     * @var array $row
     */
    protected $row = array();

    /**
     * Create Action Plan + Assign to User
     *
     * <code>
     * <?php
     *
     * $authuser = Auth::get();
     *
     * // Assign Action Plan
     * if ($plan = Backend_ActionPlan::load($_GET['plan_id'])) {
     *     $plan->assign($_GET['user_id'], $authuser);
     * }
     *
     * ?>
     * </code>
     *
     * @param array $row Data Row
     */
    public function __construct($row = array(), DB $db = null)
    {

        // ActionPlan ID
        if (isset($row['id'])) {
            $this->id = $row['id'];
        }

        // ActionPlan Row
        $this->row = $row;

        // CMS DB
        $this->db = !is_null($db) ? $db : DB::get('cms');
    }

    /**
     * Load Backend_ActionPlan by ID
     * @param int $id ActionPlan ID
     */
    public static function load($id)
    {

        // App DB
        $db = DB::get('cms');

        // Fetch DB Row
        $row = $db->getCollection(TABLE_ACTIONPLANS)->getRow($id);

        // Return Instance
        return new self ($row, $db);
    }

    /**
     * Save changes to plan info
     * @param DB $db
     * @throws Exception_ValidationError
     */
    public function save(DB $db = null)
    {

        // Database
        $db = is_null($db) ? $this->db : $db;

        // ActionPlan Collection
        $action_plans = $db->getCollection(TABLE_ACTIONPLANS);

        // ActionPlan Row
        $row = $this->getRow();

        // Require Name
        if (empty($row['name'])) {
            throw new Exception_ValidationError('Missing Action Plan Name');
        }

        // Existing ActionPlan
        if (!empty($this->id)) {
            // Timestamp Updated
            $row = array_merge($row, array(
                "`timestamp_updated` = NOW()"
            ));

            // Update Existing Row
            return $action_plans->update($row, array('$eq' => array('id' => $this->id)));
        } else {
            // Timestamp Data
            $row = array_merge($row, array(
                "`timestamp_created` = NOW()",
                "`timestamp_updated` = NOW()"
            ));

            // Insert Row
            $this->row = $action_plans->insert($row);

            // ActionPlan ID
            $this->id = $this->row['id'];

            // Success
            return true;
        }
    }

    /**
     * Delete Action Plan Record
     */
    public function delete()
    {
        // Delete Row
        if ($this->db->query("DELETE FROM `" . TABLE_ACTIONPLANS . "` WHERE `id` = '" . $this->getId() . "';")) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Add a Task to this plan
     * @param Backend_Task $task
     */
    public function addTask(Backend_Task $task)
    {

        $task->info('actionplan_id', $this->getId());

        if ($task->save($this->db)) {
            // return ID on success so it can be used to parent other tasks?
            return $task->getId();
        } else {
            // Error
            return false;
        }
    }

    /**
     * Remove a Task - If the task is a parent, transfer any children to task above
     * @param int $task_id
     */
    public function removeTask($task_id)
    {

        $task = Backend_Task::load($task_id);

        $parent_id = $task->info('parent_id');

        // Move any children of the parent of this task
        $this->db->query("UPDATE `" . TABLE_TASKS . "` SET `parent_id` = " . (!empty($parent_id) ? $this->db->quote($parent_id) : "NULL") . " WHERE `parent_id` = " . $this->db->quote($task_id) . ";");

        // Delete this task
        $this->db->query("DELETE FROM `" . TABLE_TASKS . "` WHERE `id` = " . $this->db->quote($task_id) . ";");
    }

    /**
     * Assign an Action Plan and associated tasks to specified lead
     * @param int $user_id
     */
    public function assign($user_id, Auth $authuser = null)
    {

        // Must be a saved action plan
        if (empty($this->id)) {
            return false;
        }

        // Check if already assigned to plan
        $query = $this->db->prepare("SELECT `user_id` FROM `" . TABLE_USERS_ACTIONPLANS . "` WHERE `actionplan_id` = :actionplan_id AND `user_id` = :user_id;");
        $query->execute(array('actionplan_id' => $this->getId(), 'user_id' => $user_id));
        $check = $query->fetchColumn();

        // Already assigned - do not continue
        if (!empty($check)) {
            throw new Exception('Unable to assign Action Plan! This lead has already been assigned the specified plan.');
            return;
        }

        // Insert record to assign plan
        $query = sprintf(
            "INSERT INTO `%s` SET `actionplan_id` = '%s', `user_id` = '%s'%s, `timestamp_assigned` = NOW();",
            TABLE_USERS_ACTIONPLANS,
            $this->getId(),
            $user_id,
            (!$this->hasTasks() ? ', `timestamp_completed` = NOW() ' : '')
        );

        if ($this->db->query($query)) {
            // Get the root tasks associated with this plan (followups are scheduled when parent task is completed).
            $tasks = $this->db->fetchAll(
                sprintf(
                    "SELECT * FROM `%s` WHERE `actionplan_id` = '%s' AND `parent_id` IS NULL %s;",
                    TABLE_TASKS,
                    $this->getId(),
                    Backend_Task::getDisabledTaskTypes()
                )
            );

            // Adjust due time if necessary, based on the Action Plan day and hour adjustment settings
            $allowed_days = explode(',', $this->row['day_adjust']);

            foreach ($tasks as $task) {
                // Use Backend_Task
                $task = Backend_Task::create($task);

                // Schedule Task
                $task->schedule($user_id, $allowed_days);
            }

            // Log Event: Add Action Plan
            $event = new History_Event_Update_ActionPlanAssign(array(
                'action_plan' => $this->info('name')
            ), array(
                    new History_User_Lead($user_id),
                (!empty($authuser) ? $authuser->getHistoryUser() : null)
            ));

            // Save to DB
            $event->save($this->db);

            // If there are no tasks to assign track auto completion.
            if(!$this->hasTasks()) {
                $performer = array('id' => $authuser->info('id'), 'type' => $authuser->getType());

                // Log Event: Action Plan Completed
                $event = new History_Event_Update_ActionPlanComplete(array(
                    'action_plan' => $this->row['plan_name']
                ), array(
                    new History_User_Lead($user_id),
                    (!empty($performer) ? $this->performerToHistoryUser($performer) : null)
                ));

                // Save Event
                $event->save($this->db);
            }

            return true;
        }
    }

    /**
     * Unassign action plan and remove associated tasks from specified lead
     * @param int $user_id
     */
    public function unassign($user_id, Auth $authuser = null)
    {

        // Must be an existing action plan
        if (empty($this->id)) {
            return false;
        }

        $query = "DELETE FROM `" . TABLE_USERS_ACTIONPLANS . "` WHERE `user_id` = " . $this->db->quote($user_id) . " AND `actionplan_id` = '" . $this->getId() . "';";

        if ($this->db->query($query)) {
            // Delete all tasks associated with this plan for the specified lead.
            $this->db->query("DELETE FROM `" . TABLE_USERS_TASKS . "` WHERE `actionplan_id` = '" . $this->getId() . "' AND `user_id` = " . $this->db->quote($user_id) . ";");

            // Log Event: Remove Action Plan
            $event = new History_Event_Update_ActionPlanUnAssign(array(
                'action_plan' => $this->info('name')
            ), array(
                new History_User_Lead($user_id),
                (!empty($authuser) ? $authuser->getHistoryUser() : null)
            ));

            // Save to DB
            $event->save($this->db);

            return true;
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
     * @return int Lead ID
     */
    public function getId()
    {
        return intval($this->id);
    }

    /**
     * Get Row
     *
     * @return array Lead Row
     */
    public function getRow()
    {
        return $this->row;
    }

    /************************* ArrayAccess *************************/

    /**
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($index)
    {
        return isset($this->row[$index]);
    }

    /**
     * @see ArrayAccess::offsetGet()
     */
    public function &offsetGet($index)
    {
        if ($this->offsetExists($index)) {
            return $this->row[$index];
        }
        return false;
    }

    /**
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($index, $value)
    {
        if ($index) {
            $this->row[$index] = $value;
        } else {
            $this->row[] = $value;
        }
        return true;
    }

    /**
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($index)
    {
        unset($this->row[$index]);
        return true;
    }
    
    /**
     * Returns true if a action plan has at least one task, otherwise false
     * @return bool
     */
    public function hasTasks()
    {
        $db = DB::get('cms');
        $collection = $db->getCollection(TABLE_TASKS);
        $value = intval($collection->count([
            '$eq' => ['actionplan_id' => $this->getId()]
        ]));
        return $value > 0;
    }
}
