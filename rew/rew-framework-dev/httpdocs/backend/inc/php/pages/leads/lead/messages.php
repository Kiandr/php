<?php

/**
 * IDX Control Panel Not Enabled, Re-Direct
 */
if (!isset(Settings::getInstance()->MODULES['REW_IDX_CP']) || empty(Settings::getInstance()->MODULES['REW_IDX_CP'])) {
    header('Location: ' . URL_BACKEND . 'leads/');
    exit;
}

// App DB
$db = DB::get();

// App Settings
$settings = Settings::getInstance();

// Success
$success = array();

// Error
$errors = array();

// Lead ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

try {
    // Query Lead
    $lead = $db->fetch("SELECT * FROM `" . LM_TABLE_LEADS . "` WHERE `id` = :id;", ['id' => $_GET['id']]);

    // Throw Missing $lead Exception
    if (empty($lead)) {
        throw new \REW\Backend\Exceptions\MissingId\MissingLeadException();
    }
} catch (PDOException $e) {
    Log::error($e);

    throw new \REW\Backend\Exceptions\SystemErrorException();
}

// Create lead instance
$lead = new Backend_Lead($lead);

// Get Lead Authorization
$leadAuth = new REW\Backend\Auth\Leads\LeadAuth($settings, $authuser, $lead);

// Not authorized to view all lead messages
if (!$leadAuth->canViewMessages()) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have permission to view lead messages'
    );
}

// Category ID
$_GET['category'] = isset($_POST['category']) ? $_POST['category'] : $_GET['category'];

// Edit Row
$_GET['edit'] = isset($_POST['edit']) ? $_POST['edit'] : $_GET['edit'];

/**
 * Delete Message
 */
if (!empty($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (!$leadAuth->canManageLead()) {
        $errors[] = 'You are not authorized to delete this message.';
    } else {
        try {
            // Build DELETE Query
            $stmt = $db->prepare("DELETE FROM `" . LM_TABLE_MESSAGES . "` WHERE (`id` = :id OR `category` = :category) AND `user_id` = :user_id;");

            $stmt->execute([
                ':id' => $_GET['delete'],
                ':category' => $_GET['delete'],
                ':user_id' => $lead['id']
            ]);
        } catch (PDOException $e) {
            // Query Error
            $errors[] = 'An error occurred while trying to delete the selected message.';

            Log::error($e);
        }

        // Success
        $success[] = 'The selected message has been deleted.';
    }
    $authuser->setNotices($success, $errors);
    $uri = '?id=' . $lead->getId() . ($_GET['category'] ? '&category=' . $_GET['category'] : '');
    header('Location: /backend/leads/lead/messages/' . $uri);
    exit;
}

/**
 * Get Message to edit
 */
if (!empty($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editMessage = $db->fetch(
        "SELECT * FROM `" . LM_TABLE_MESSAGES . "` WHERE `id` = :id AND `user_id` = :user_id AND `sent_from` = 'agent' LIMIT 1;",
        [':id' => $_GET['edit'], ':user_id' => $lead['id']]
    );

    if (empty($editMessage)) {
        throw new MissingIdException();
    }
}

/**
 * Handle Form Submissions
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /**
     * Edit Message
     */
    if (!empty($_POST['edit'])) {
        if (!$leadAuth->canManageLead()) {
            $errors[] = 'You are not authorized to edit this message.';
        } else {
            try {
                // Required Fields
                $required = array();
                $required[] = array('value' => 'message', 'title' => 'Message');

                // Process Required Fields
                foreach ($required as $require) {
                    if (empty($_POST[$require['value']])) {
                        $errors[] = $require['title'] . ' is a required field.';
                    }
                }

                // Check Errors
                if (empty($errors)) {
                    try {
                        // Build UPDATE Query
                        $stmt = $db->prepare(
                            "UPDATE `" . LM_TABLE_MESSAGES . "` SET "
                            . "`message` = :message"
                            . " WHERE "
                            . "`user_id` = :user_id AND "
                            . "`id` = :id;"
                        );

                        $stmt->execute([
                            ':message' => $_POST['message'],
                            ':user_id' => $lead['id'],
                            ':id'      => $_POST['edit']
                        ]);
                    } catch (PDOException $e) {
                        // Query Error
                        $errors[] = 'An error occurred while trying to update the selected message.';

                        Log::error($e);
                    }

                    // Success
                    $success[] = 'The selected message has been updated.';

                    // Unset
                    unset($_GET['edit']);
                }
            } catch (PDOException $e) {
                // Query Error
                $errors[] = 'Error Occurred while loading the selected message.';

                Log::error($e);
            } catch (MissingIdException $e) {
                // Row not Found
                $errors[] = 'The selected message could not be found.';

                Log::error($e);
            }
        }
    } else {

        /**
         * Send New Message
         */
        if (isset($_POST['submit'])) {
            // Required Fields
            $required = array();
            $required[] = array('value' => 'subject', 'title' => 'Subject');
            $required[] = array('value' => 'message', 'title' => 'Message');

            // Process Required Fields
            foreach ($required as $require) {
                if (empty($_POST[$require['value']])) {
                    $errors[] = $require['title'] . ' is a required field.';
                }
            }

            // Check Errors
            if (empty($errors)) {
                try {
                    // Generate INSERT Query
                    $stmt = $db->prepare(
                        "INSERT INTO `" . LM_TABLE_MESSAGES . "` SET "
                        . "`user_id`    = :user_id, "
                        . "`agent_id`   = :agent_id, "
                        . "`subject`    = :subject, "
                        . "`message`    = :message, "
                        . "`reply`      = 'N', "
                        . "`sent_from`  = 'agent', "
                        . "`agent_read` = 'Y', "
                        . "`timestamp`  = NOW();"
                    );

                    $stmt->execute([
                        ':user_id' => $lead['id'],
                        ':agent_id' => $authuser->info('id'),
                        ':subject' => $_POST['subject'],
                        ':message' => $_POST['message']
                    ]);

                    $insert_id = $db->lastInsertId();

                    $stmt = $db->prepare("UPDATE `" . LM_TABLE_MESSAGES . "` SET `category` = :category WHERE `id` = :id;");

                    $stmt->execute([
                        ':category' => $insert_id,
                        ':id' => $insert_id
                    ]);

                    // Create Email
                    $mailer = new \PHPMailer\RewMailer();
                    $mailer->CharSet = 'UTF-8';
                    $mailer->IsHTML(true);

                    // Sender
                    $from_name = $authuser->info('first_name') . ' ' . $authuser->info('last_name');
                    $from_email = $authuser->info('email');
                    $mailer->From = $from_email;
                    $mailer->FromName = $from_name;

                    // Recipient
                    $mailer->AddAddress($lead['email'], $lead['first_name'] . ' ' . $lead['last_name']);

                    // Email Subject
                    $mailer->Subject = htmlspecialchars_decode($_POST['subject']);

                    // Email Message (HTML)
                    $mailer->Body = '';
                    $mailer->Body .= '<p><b>' . $authuser->info('first_name') . ' ' . $authuser->info('last_name') . '</b> has sent you a new message:</p>';
                    $mailer->Body .= '<p>' . $_POST['message'] . '<br />';
                    $mailer->Body .= '<p>' . str_repeat('-', 50) . '</p>';
                    $mailer->Body .= '<p>To reply to this message and to view any other messages you might have, log-in to your Private Control Panel at <a href="' . Settings::getInstance()->SETTINGS['URL_IDX'] . '?dashboard">' . Settings::getInstance()->SETTINGS['URL_IDX'] . '</a></p>';
                    $mailer->Body .= '<p><b>As a Reminder... </b><br />';
                    $mailer->Body .= 'Username: ' . $lead['email'] . '<br />';
                    $mailer->Body .= '<p>Thank you for working with us!</p>';

                    // Send Email
                    $mailer->Send();

                    // Unset Data
                    unset($_POST['subject'], $_POST['message']);

                    // Success
                    $success[] = 'The new message has successfully been sent.';
                } catch (Exception $e) {
                    // Query Error
                    $errors[] = 'An error occurred while attempting to send your message.';

                    Log::error($e);
                }
            }
        }

        /**
         * Reply to Message
         */
        if (isset($_POST['reply'])) {
            // Required Fields
            $required = array();
            $required[] = array('value' => 'message', 'title' => 'Message');

            // Process Required Fields
            foreach ($required as $require) {
                if (empty($_POST[$require['value']])) {
                    $errors[] = $require['title'] . ' is a required field.';
                }
            }

            // Check Errors
            if (empty($errors)) {
                try {
                    $msg = $db->fetch("SELECT * FROM `" . LM_TABLE_MESSAGES . "` WHERE `id` = :id AND `user_del` = 'N' LIMIT 1;", [':id' => $_POST['msg_id']]);

                    if (empty($msg)) {
                        throw new MissingIdException();
                    }

                    // Build INSERT Query
                    $stmt = $db->prepare(
                        "INSERT INTO `" . LM_TABLE_MESSAGES . "` SET "
                        . "`user_id`    = :user_id, "
                        . "`agent_id`   = :agent_id, "
                        . "`subject`    = :subject, "
                        . "`message`    = :message, "
                        . "`reply`      = 'Y', "
                        . "`sent_from`  = 'agent', "
                        . "`category`   = :category, "
                        . "`agent_read` = 'Y', "
                        . "`timestamp`  = NOW();"
                    );

                    $stmt->execute([
                        ':user_id' => $lead['id'],
                        ':agent_id' => $authuser->info('id'),
                        ':subject' => $msg['subject'],
                        ':message' => $_POST['message'],
                        ':category' => $msg['category']
                    ]);

                    // Create Email
                    $mailer = new \PHPMailer\RewMailer();
                    $mailer->CharSet = 'UTF-8';
                    $mailer->IsHTML(true);

                    // Sender
                    $from_name = $authuser->info('first_name') . ' ' . $authuser->info('last_name');
                    $from_email = $authuser->info('email');
                    $mailer->From = $from_email;
                    $mailer->FromName = $from_name;

                    // Recipient
                    $mailer->AddAddress($lead['email'], $lead['first_name'] . ' ' . $lead['last_name']);

                    // Email Subject
                    $mailer->Subject = htmlspecialchars_decode('RE: ' . $msg['subject']);

                    // Email Message (HTML)
                    $mailer->Body = '';
                    $mailer->Body .= '<p><b>' . $authuser->info('first_name') . ' ' . $authuser->info('last_name') . '</b> has replied to one of your messages:</p>';
                    $mailer->Body .= '<p>' . $_POST['message'] . '</p>';
                    $mailer->Body .= '<p>' . str_repeat('-', 50) . '</p>';
                    $mailer->Body .= '<p>To reply to this message and to view any other messages you might have, log-in to your Private Control Panel at <a href="' . Settings::getInstance()->SETTINGS['URL_IDX'] . '?dashboard">' . Settings::getInstance()->SETTINGS['URL_IDX'] . '</a></p>';
                    $mailer->Body .= '<p>Thank you for working with us!</p>';

                    // Send Email
                    $mailer->Send();

                    // Success
                    $success[] = 'Your reply has successfully been sent.';
                } catch (PDOException $e) {
                    // Query Error
                    $errors[] = 'An error occurred while attempting to send your message.';

                    Log::error($e);
                } catch (MissingIdException $e) {
                    $errors[] = 'The thread you are attempting to reply to does not exist.';

                    Log::error($e);
                }
            }
        }
    }

    $authuser->setNotices($success, $errors);
    $uri = '?id=' . $lead->getId() . ($_GET['category'] ? '&category=' . $_GET['category'] : '');
    header('Location: /backend/leads/lead/messages/' . $uri);
    exit;
}

/**
 * Start Messages
 * Get Lead Messages from category
 */
if (!empty($_GET['category']) && is_numeric($_GET['category'])) {
    // Check Whether Thread Has Been Deleted
    try {
        $result = $db->fetch("SELECT `user_del` FROM `" . LM_TABLE_MESSAGES . "` WHERE `id` = :id", [':id' => $_GET['category']]);
        $thread_deleted = $result['user_del'] === 'Y';
    } catch (PDOException $e) {
        $errors[] = 'Unable to check whether thread has been deleted';
    }

    $myMessages = array();

    $i = 0;

    try {
        $stmt = $db->prepare("SELECT * FROM `" . LM_TABLE_MESSAGES . "` WHERE `user_id` = :user_id AND `category` = :category ORDER BY CAST(`reply` AS CHAR), `timestamp` ASC;");

        $stmt->execute([
            ':user_id'  => $lead['id'],
            ':category' => $_GET['category']
        ]);

        while ($message = $stmt->fetch()) {
            // Set as Read
            $update = $db->prepare("UPDATE `" . LM_TABLE_MESSAGES . "` SET `agent_read` = 'Y' WHERE `agent_id` = :agent_id AND `id` = :id;");

            $update->execute([
                ':agent_id' => $authuser->info('id'),
                ':id'       => $message['id']
            ]);

            // Message ID
            $message_id = $message['category'];

            // Message Subject
            $subject = $message['subject'];

            // Set Message Details
            $myMessages[$i]['id']         = $message['id'];
            $myMessages[$i]['category']   = $message['category'];
            $myMessages[$i]['timestamp']  = $message['timestamp'];
            $myMessages[$i]['reply']      = $message['reply'];
            $myMessages[$i]['user_read']  = $message['user_read'];
            $myMessages[$i]['agent_read'] = $message['agent_read'];
            $myMessages[$i]['user_id']    = $message['user_id'];
            $myMessages[$i]['agent_id']   = $message['agent_id'];
            $myMessages[$i]['user_del']   = $message['user_del'];

            // Select Agent Details
            $agentName = $db->fetch("SELECT `first_name`, `last_name` FROM `" . LM_TABLE_AGENTS . "` WHERE `id` = :id LIMIT 1;", [':id' => $message['agent_id']]);

            // Select Lead Details
            $leadName = $db->fetch("SELECT `first_name`, `last_name`, `email` FROM `" . LM_TABLE_LEADS . "` WHERE `id` = :id LIMIT 1;", [':id' => $message['user_id']]);
            $leadName['name'] = (empty($leadName['first_name']) || empty($leadName['last_name'])) ? $leadName['email'] : $leadName['first_name'] . ' ' . $leadName['last_name'];

            // Sent from Agent
            if ($message['sent_from'] == 'agent') {
                $myMessages[$i]['sent_from'] = $agentName['first_name'] . ' ' . $agentName['last_name'];
                $myMessages[$i]['sent_to'] = $leadName['name'];
                $myMessages[$i]['message'] = $message['message'];
                $myMessages[$i]['editable']  = true;

            // Sent from Lead
            } else {
                $myMessages[$i]['sent_to'] = $agentName['first_name'] . ' ' . $agentName['last_name'];
                $myMessages[$i]['sent_from'] = $leadName['name'];
                $myMessages[$i]['message'] = nl2br($message['message']);
                $myMessages[$i]['editable']  = false;
            }

            $i++;
        }
    } catch (PDOException $e) {
        // Query Error
        $errors[] = 'Error Occurred while loading Messages.';
    }
}

/**
 * Load Messages
 */
$order = array();
$messages = array();
try {
    $params = array();

    $params[':user_id'] = $lead['id'];

    if ($authuser->info('id') != 1) {
        $agent_id = " AND `agent_id` = :agent_id";

        $params[':agent_id'] = $authuser->info('id');
    }

    $stmt = $db->prepare("SELECT * FROM `" . LM_TABLE_MESSAGES . "` WHERE `user_id` = :user_id AND `reply` = 'N'" . $agent_id . " ORDER BY `timestamp` DESC;");

    $stmt->execute($params);

    while ($message = $stmt->fetch()) {
        // Check If Lead has Read Message
        $checkRead = $db->fetch(
            "SELECT COUNT(*) AS `total` FROM `" . LM_TABLE_MESSAGES . "` WHERE `user_read` = 'N' AND `category` = :category AND `user_id` = :user_id;",
            [
                ':category' => $message['category'],
                ':user_id'  => $lead['id']
            ]
        );
        $message['user_read'] = ($checkRead['total'] > 0) ? 'N' : 'Y';

        // Check If Agent has Read Message
        $checkRead = $db->fetch(
            "SELECT COUNT(*) AS `total` FROM `" . LM_TABLE_MESSAGES . "` WHERE `agent_read` = 'N' AND `category` = :category AND `user_id` = :user_id;",
            [
                ':category' => $message['category'],
                ':user_id'  => $lead['id']
            ]
        );
        $message['agent_read'] = ($checkRead['total'] > 0) ? 'N' : 'Y';

        // Sent from Agent
        if ($message['sent_from'] == 'agent') {
            $sender = $db->fetch("SELECT `first_name`, `last_name` FROM `" . LM_TABLE_AGENTS . "` WHERE `id` = :id LIMIT 1;", [':id' => $message['agent_id']]);

        // Sent from Lead
        } else {
            $sender = $db->fetch("SELECT `first_name`, `last_name` FROM `" . LM_TABLE_LEADS . "` WHERE `id` = :id LIMIT 1;", [':id' => $message['user_id']]);
        }

        // Sent From
        $message['sent_from'] = $sender['first_name'] . ' ' . $sender['last_name'];

        // Get Latest Timestamp
        $lastest = $db->fetch(
            "SELECT `timestamp` FROM `" . LM_TABLE_MESSAGES . "` WHERE `user_id` = :user_id AND `category` = :category ORDER BY `timestamp` DESC LIMIT 1;",
            [
                ':user_id'  => $lead['id'],
                ':category' => $message['id'],
            ]
        );
        $message['latest'] = $lastest['timestamp'];

        // Get Message Count
        $count = $db->fetch(
            "SELECT COUNT(*) AS `total` FROM `" . LM_TABLE_MESSAGES . "` WHERE `user_id` = :user_id AND `category` = :category;",
            [
                ':user_id'  => $lead['id'],
                ':category' => $message['id']
            ]
        );
        $message['count'] = $count['total'];

        // Order by Latest Timestamp
        $order[] = !empty($message['latest']) ? strtotime($message['latest']) : strtotime($message['timestamp']);

        // Append to Messages
        $messages[] = $message;
    }
} catch (PDOException $e) {
    // Query Error
    $errors[] = 'Unable to load messages.';

    Log::error($e);
}

// Re-Order Latest Messages
array_multisort($order, SORT_DESC, $messages);
