<?php
/**
 * Use this Trait for Action History Events that need to update Leads last_action
 * @package History
 */
trait History_Trait_LastActionEvent
{
    /**
     * Update Leads last_action
     * @return NULL|string
     */
    protected function saveLastActionEvent () {
        // cache the data to the DB with whatever information is required to display
        $this->db->prepare("
            UPDATE `users`
            SET last_action = :last_action
            where FIND_IN_SET(id, :ids);
        ")->execute([
            'last_action' => $this->getID(),
            'ids'         => implode(',', array_filter(array_map(function ($user) { return $user->getType() === History_User::TYPE_LEAD ? $user->getUserData('id') : false; }, $this->getUsers())))
        ]);
    }

}