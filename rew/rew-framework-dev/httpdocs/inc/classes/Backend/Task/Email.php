<?php

class Backend_Task_Email extends Backend_Task
{

    /**
     * Load email subject, body, doc_id
     * @see Backend_Task::loadTaskContent()
     */
    protected function loadTaskContent()
    {

        // Require task ID
        if (!empty($this->id)) {
            if ($content = $this->db->fetch(sprintf(
                "SELECT `subject`, `body`, `doc_id` FROM `%s` WHERE `task_id` = :task_id;",
                $this->settings->TABLES['LM_TASK_EMAILS']
            ), [
                'task_id' => $this->id
            ])) {
                $this->row = array_merge($this->row, $content);
            }
        }
    }

    /**
     * Save email subject, body, doc_id
     * @see Backend_Task::saveTaskContent()
     */
    protected function saveTaskContent()
    {

        // Validate Body
        if (empty($this->row['body'])) {
            throw new Exception_ValidationError('Email Message Required');
        }

        if (!empty($this->row['body']) || !empty($this->row['doc_id'])) {
            $query = $this->db->prepare(sprintf(
                "INSERT INTO `%s` SET "
                    . " `task_id` = :task_id, "
                    . " `subject` = :subject, "
                    . " `body` = :body, "
                    . " `doc_id` = :doc_id "
                . " ON DUPLICATE KEY UPDATE "
                    . " `subject` = :subject, "
                    . " `body` = :body, "
                    . " `doc_id` = :doc_id "
                . ";",
                $this->settings->TABLES['LM_TASK_EMAILS']
            ));
            $query->execute(array('task_id' => $this->id, 'subject' => $this->row['subject'], 'body' => $this->row['body'], 'doc_id' => $this->row['doc_id']));
        }
    }

    public function __construct($row = array())
    {
        parent::__construct($row);

        if (!empty($this->row['doc_id']) && empty($this->row['body'])) {
            $document = $this->db->fetch("SELECT `document` FROM `docs` WHERE `id` = :doc_id LIMIT 1;", array('doc_id' => $this->row['doc_id']));
            $this->row['body'] = $document['document'];
        }
    }

    /**
     * POST email subject, body
     * @see Backend_Task::postTaskContent()
     */
    public function postTaskContent()
    {

        $_POST['email_subject'] = $this->row['subject'];

        if (!empty($this->row['doc_id'])) {
            $_POST['doc_id'] = $this->row['doc_id'];
            $document = $this->db->fetch("SELECT `document` FROM `docs` WHERE `id` = :doc_id LIMIT 1;", array('doc_id' => $this->row['doc_id']));
            $_POST['email_message'] = $document['document'];
        } else {
            $_POST['email_message'] = $this->row['body'];
        }
        return true;
    }

    /**
     * Send email to specified user using email subject/body associated with this task.
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
            $subject = $this->row['subject'];
            $performer = $this->row['performer'];
            $message = $this->row['body'];

            if (empty($message)) {
                if ($e_output) {
                    echo 'Error! No message attached to email task.';
                }
                return false;
            }

            if (!empty($subject) && !empty($message)) {
                // Set up mailer
                $mailer = new Backend_Mailer(array(
                    'subject' => $subject,
                    'message' => $message
                ));

                $mailer->setRecipient($lead->getEmail(), $lead->getName());

                // Mailer Tags
                $tags = array(
                    'first_name' => $lead['first_name'],
                    'last_name'  => $lead['last_name'],
                    'email'      => $lead['email'],
                    'guid'       => Format::toGuid($lead['guid']),
                    'verify'     => $this->settings->SETTINGS['URL_IDX'] . 'verify.html?verify=' . Format::toGuid($lead['guid']),
                );

                // Figure out who the sender is (assigned agent/lender - based on task performer)
                if ($performer == 'Agent') {
                    $table = 'agents';
                    $performer_id = $lead['agent'];
                } else if ($performer == 'Lender') {
                    $table = 'lenders';
                    $performer_id = $lead['lender'];
                } else {
                    if ($e_output) {
                        echo 'Invalid performer';
                    }
                    return false;
                }

                $sender = $this->db->fetch("SELECT CONCAT(`first_name`, ' ', `last_name`) as `name`, `email`" . ($performer == 'Agent' ? ", `signature`" : "") . " FROM `" . $table . "` WHERE `id` = :performer_id;", array('performer_id' => $performer_id));

                if (!empty($sender['signature'])) {
                    $tags = array_merge(array(
                        'signature'   => $sender['signature'],
                    ), $tags);
                }

                if (!empty($sender)) {
                    $mailer->setSender($sender['email'], $sender['name']);
                    // Send
                    if ($mailer->Send($tags)) {
                        // Get Auth User For History Tracking
                        $authuser = Auth::get();

                        // Log Event: Email Sent to Lead
                        $event = new History_Event_Email_Direct(array(
                            'plaintext' => !$mailer->isHTML(),
                            'subject'   => $mailer->getSubject(),
                            'message'   => $mailer->getMessage(),
                            'tags'      => $mailer->getTags()
                        ), array(
                            new History_User_Lead($lead['id']),
                            (($authuser->isValid()) ? $authuser->getHistoryUser() : null)
                        ));

                        $event->save();

                        // Resolve the task
                        if (!empty($performer_id)) {
                            $resolve_performer = array('id' => $performer_id, 'type' => $performer);
                        } else {
                            $resolve_performer = array('id' => null, 'type' => 'System');
                        }
                        return $this->resolve($user_id, $resolve_performer, 'Completed', ($automated ? 'Automated Task' : null));
                    } else {
                        if ($e_output) {
                            echo 'Email Task Error! Mailer error.';
                        }
                        return false;
                    }
                } else {
                    if ($e_output) {
                        echo 'Email Task Error! Unable to find Sender.';
                    }
                    return false;
                }
            }
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
        return URL_BACKEND . 'email/?id=' . $user_id;
    }

    /**
     * @see Backend_Task::getEventTypes()
     */
    public function getEventTypes()
    {
        return array('Email_Direct', 'Email Delayed');
    }
}
