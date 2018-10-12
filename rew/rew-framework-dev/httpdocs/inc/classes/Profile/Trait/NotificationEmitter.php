<?php

/**
 * Profile_Trait_NotificationEmitter
 *
 */
trait Profile_Trait_NotificationEmitter
{

    /**
     * Collection of notification handlers to message
     * @var array
     */
    private $_notification_handlers = array();

    /**
     * Register a notification handler to receive notifications posted by this object
     * @param Profile_Interface_NotificationHandler $handler
     */
    public function registerNotificationHandler(Profile_Interface_NotificationHandler $handler)
    {
        $this->_notification_handlers[] = $handler;
    }

    /**
     * Post a new notification to all notification handlers
     * @param int $type
     * @param mixed $data
     */
    protected function postNotification($type, $data = null)
    {
        foreach ($this->_notification_handlers as $handler) {
            $handler->handleNotification($type, $this, $data);
        }
    }
}
