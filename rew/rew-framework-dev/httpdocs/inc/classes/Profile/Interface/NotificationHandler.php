<?php

/**
 * Profile_Interface_NotificationHandler
 * Objects implementing this interface can subscribe to and receive notifications from objects using the Profile_Trait_Observable trait
 *
 */
interface Profile_Interface_NotificationHandler
{

    /**
     * Handle a notification triggered by a notification emitter
     * @param int $type
     * @param mixed $sender
     * @param mixed $data
     */
    public function handleNotification($type, $sender, $data = null);
}
