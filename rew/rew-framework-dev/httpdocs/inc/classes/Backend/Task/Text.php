<?php

class Backend_Task_Text extends Backend_Task
{

    /**
     * Load text message
     * @see Backend_Task::loadTaskContent()
     */
    protected function loadTaskContent()
    {
        // Require task ID
        if (!empty($this->id)) {
            $content = $this->db->fetch(sprintf(
                "SELECT `message` FROM `%s` WHERE `task_id` = :task_id;",
                $this->settings->TABLES['LM_TASK_TEXTS']
            ), [
                'task_id' => $this->id
            ]);
            $this->row['message'] = $content['message'];
        }
    }

    /**
     * Save text message
     * @see Backend_Task::saveTaskContent()
     */
    protected function saveTaskContent()
    {
        if (!empty($this->row['message'])) {
            $query = $this->db->prepare(sprintf(
                "INSERT INTO `%s` SET `task_id` = :task_id, `message` = :message ON DUPLICATE KEY UPDATE `message` = :message;",
                $this->settings->TABLES['LM_TASK_TEXTS']
            ));
            $query->execute(array('message' => $this->row['message'], 'task_id' => $this->id));
        }
    }

    /**
     * POST text message
     * @see Backend_Task::postTaskContent()
     */
    public function postTaskContent()
    {
        $_POST['body'] = $this->row['message'];
        return true;
    }

    /**
     * Send text via REWText to specified user using data associated with this task.
     *
     * @param int $user_id The ID of the user for which to run this task.
     * @param bool $automated Detemines whether this task is being processed via an automated script
     * @param bool $e_output Determines whether errors will be echoed or suppressed
     *
     * @return bool
     *
     * @see Backend_Task::processAndResolve()
     */
    public function processAndResolve($user_id, $automated = false, $e_output = false)
    {
        // Auto send text message via REWText (Twilio) if available
        if (!empty($this->settings->MODULES['REW_PARTNERS_TWILIO'])) {
            // Require lead
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

                try {
                    // Send to lead's cell phone #
                    $to = $lead->info('phone');
                    if (empty($to)) {
                        return null; // No known cell #
                    }

                    // Twilio API Client
                    $twilio = Partner_Twilio::getInstance();

                    // Choose first available number to send from
                    $numbers = $twilio->getTwilioNumbers();
                    if (empty($numbers)) {
                        return null; // No available phone numbers
                    }
                    $from = $numbers ? array_pop($numbers)['phone_number'] : false;

                    // Format message body
                    $body = $this->row['message'];
                    $body = str_replace('{first_name}', $lead->info('first_name'), $body);
                    $body = str_replace('{last_name}', $lead->info('last_name'), $body);

                    // Send SMS
                    if ($twilio->sendSmsMessage($to, $from, $body)) {
                        // Track text message auto-responder
                        (new History_Event_Text_Outgoing(array(
                            'to'   => $to,
                            'from' => $from,
                            'body' => $body,
                        ), array(
                            new History_User_Lead($lead->getId()),
                            (($authuser->isValid()) ? $authuser->getHistoryUser() : null)
                        )))->save();

                        // Resolve Task
                        if ($authuser->isValid()) {
                            $resolve_performer = array('id' => $authuser->info('id'), 'type' => $authuser->getType());
                        } else if (!empty($performer_id)) {
                            $resolve_performer = array('id' => $performer_id, 'type' => $performer);
                        } else {
                            $resolve_performer = array('id' => null, 'type' => 'System');
                        }
                        return $this->resolve($user_id, $resolve_performer, 'Completed', ($automated ? 'Automated Task' : null));
                    }

                // Error occurred
                } catch (Exception $e) {
                    if ($_output) {
                        echo $e->getMessage();
                    }
                    return false;
                }
            } else {
                if ($e_output) {
                    echo 'Task error! Invalid user.';
                }
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @see Backend_Task::getShortcutURL()
     */
    public function getShortcutURL($user_id, $special = false)
    {
        if ($this->settings->MODULES['REW_PARTNERS_TWILIO']) {
            return URL_BACKEND . 'leads/lead/text/?id=' . $user_id;
        }
        return false;
    }

    /**
     * @see Backend_Task::getEventTypes()
     */
    public function getEventTypes()
    {
        return array('Text_Outgoing');
    }
}
