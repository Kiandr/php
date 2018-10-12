<?php

class Backend_Task_Search extends Backend_Task
{

    /**
     * @see Backend_Task::loadTaskContent()
     */
    protected function loadTaskContent()
    {
    }

    /**
     * @see Backend_Task::saveTaskContent()
     */
    protected function saveTaskContent()
    {
    }

    /**
     * @see Backend_Task::postTaskContent()
     */
    public function postTaskContent()
    {
        return false;
    }

    /**
     * @param int $user_id The ID of the user for which to run this task
     * @param bool $automated Detemines whether this task is being processed via an automated script
     * @param bool $e_output Determines whether errors will be echoed or suppressed
     *
     * @see Backend_Task::processAndResolve()
     */
    public function processAndResolve($user_id, $automated = false, $e_output = false)
    {
        return false;
    }

    /**
     * @see Backend_Task::getShortcutURL()
     */
    public function getShortcutURL($user_id, $special = false)
    {
        return Settings::getInstance()->URLS['URL_BACKEND'] . 'idx/action_plan_search_form/?popup=true&lead_id=' . $user_id;
    }

    /**
     * @see Backend_Task::getEventTypes()
     */
    public function getEventTypes()
    {
        return array('Action_SavedSearch');
    }
}
