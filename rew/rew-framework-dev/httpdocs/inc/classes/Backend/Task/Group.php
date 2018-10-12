<?php

class Backend_Task_Group extends Backend_Task
{

    /**
     * Event Types
     * @var array $event_types
     */
    protected $event_types = array('Update_GroupAdd');

    /**
     * @see Backend_Task::loadTaskContent()
     */
    protected function loadTaskContent()
    {

        $groups = array();
        // Require task ID
        if (!empty($this->id)) {
            $query = $this->db->prepare(sprintf(
                "SELECT `group_id` FROM `%s` WHERE `task_id` = :task_id;",
                $this->settings->TABLES['LM_TASK_GROUPS']
            ));
            if ($query->execute(array('task_id' => $this->getId()))) {
                while ($group = $query->fetch()) {
                    $groups[] = $group['group_id'];
                }
            }
        }
        $this->row['groups'] = $groups;
    }

    /**
     * @see Backend_Task::saveTaskContent()
     */
    protected function saveTaskContent()
    {

        // Get current groups
        $groups = $this->db->fetchAll(sprintf(
            "SELECT `group_id` FROM `%s` WHERE `task_id` = '" . $this->getId() . "';",
            $this->settings->TABLES['LM_TASK_GROUPS']
        ));

        // Build collection of current groups
        $current_groups = array();
        foreach ($groups as $group) {
            $current_groups[] = $group['group_id'];
        }

        // Submitted groups
        $new_groups = !empty($this->row['groups']) ? $this->row['groups'] : array();

        foreach ($new_groups as $group) {
            if (!in_array($group, $current_groups)) {
                // Insert group
                $query = $this->db->prepare(sprintf(
                    "INSERT INTO `%s` SET `task_id` = :task_id, `group_id` = :group_id;",
                    $this->settings->TABLES['LM_TASK_GROUPS']
                ));
                $query->execute(array('task_id' => $this->getId(), 'group_id' => $group));
            }
        }

        foreach ($current_groups as $group) {
            if (!in_array($group, $new_groups)) {
                // Remove group
                $query = $this->db->prepare(sprintf(
                    "DELETE FROM `%s` WHERE `task_id` = :task_id AND `group_id` = :group_id;",
                    $this->settings->TABLES['LM_TASK_GROUPS']
                ));
                $query->execute(array('task_id' => $this->getId(), 'group_id' => $group));
            }
        }
    }

    /**
     * @see Backend_Task::postTaskContent()
     */
    public function postTaskContent()
    {
        return false;
    }

    /**
     * Add the specified user to the group associated with this task
     *
     * @param int $user_id The ID of the user for which to run this task
     * @param bool $automated Detemines whether this task is being processed via an automated script
     * @param bool $e_output Determines whether errors will be echoed or suppressed
     *
     * @return bool
     *
     * @see Backend_Task::processAndResolve()
     */
    public function processAndResolve($user_id, $automated = false, $e_output = false)
    {

        if ($lead = Backend_Lead::load($user_id)) {
            $authuser = Auth::get();

            $performer = $this->row['performer'];

            // Figure out who the performer is (assigned agent/lender - based on task performer)
            if ($performer == 'Agent') {
                $table = 'agents';
                $performer_id = $lead['agent'];
            } else if ($performer == 'Lender') {
                $table = 'lenders';
                $performer_id = $lead['lender'];
            }

            $groups = $this->db->fetchAll(sprintf(
                "SELECT `id`, `name` FROM `%s` WHERE `id` IN ('%s');",
                $this->settings->TABLES['LM_GROUPS'],
                implode("','", $this->row['groups'])
            ));
            if (!empty($groups)) {
                foreach ($groups as $group) {
                    // Assign Group
                    $lead->assignGroup($group, ($authuser->isValid() ? $authuser : null));
                }
                if (count($groups) < count($this->row['groups'])) {
                    $this->addNote($user_id, 'Failed to assign user to some of the requested groups. It is possible the groups were deleted from the system.');
                }
            } else {
                $this->addNote($user_id, 'Failed to assign user to the requested groups. It is possible the groups were deleted from the system.');
            }
            // Resolve Task
            if ($authuser->isValid()) {
                $resolve_performer = array('id' => $authuser->info('id'), 'type' => $authuser->getType());
            } else if (!empty($performer_id)) {
                $resolve_performer = array('id' => $performer_id, 'type' => $performer);
            } else {
                $resolve_performer = array('id' => null, 'type' => 'System');
            }
            return $this->resolve($user_id, $resolve_performer, 'Completed', ($automated ? 'Automated Task' : null));
        } else {
            if ($e_output) {
                echo 'Task error! Invalid user.';
            }
            return false;
        }
    }

    /**
     * @see Backend_Task::getShortcutURL()
     */
    public function getShortcutURL($user_id, $special = false)
    {
        return $this->settings->URLS['URL_BACKEND'] . 'leads/lead/edit/?id=' . $user_id;
    }

    /**
     * @see Backend_Task::getEventTypes()
     */
    public function getEventTypes()
    {
        return $this->event_types;
    }
}
