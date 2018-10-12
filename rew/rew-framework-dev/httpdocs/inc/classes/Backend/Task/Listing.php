<?php

class Backend_Task_Listing extends Backend_Task
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
        return URL_BACKEND . 'leads/lead/summary/?id=' . $user_id . '&action=listing';
    }

    /**
     * @see Backend_Task::getEventTypes()
     */
    public function getEventTypes()
    {
        return array('Action_SavedListing');
    }
}
