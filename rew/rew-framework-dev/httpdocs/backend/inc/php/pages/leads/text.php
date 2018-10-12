<?php

// Get Authorization Managers
$settings = Settings::getInstance();
$leadsAuth = new REW\Backend\Auth\LeadsAuth($settings);
$teamsAuth = new REW\Backend\Auth\TeamsAuth($settings);

// Get Team Manager
if ($teamsAuth->canViewTeamLeads()) {
    $teamManager = new REW\Backend\Teams\Manager($authuser);
}

$sql_agent = '';
$sql_agent_params = array();
$isAgent = $authuser->isAgent();

// Not authorized to view all leads
if (!$leadsAuth->canManageLeads($authuser)) {
    // Not authorized to view any leads
    if (!$leadsAuth->canManageOwn($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            'You do not have permission to view leads'
        );
    }

    // Is Agent: Only Show Assigned Leads
    if ($isAgent) {
        if ($teamsAuth->canViewTeamLeads()) {
            // Get Agents Sharing with this Teams
            $teamAgents = $teamManager->getAgentsSharingLeads();

            // Double check for personal leads
            if (!in_array($authuser->info('id'), $teamAgents)) {
                $teamAgents[] = $authuser->info('id');
            }

            $sql_agent = " AND `u`.`agent` IN (" . implode(', ', array_fill(0, count($teamAgents), '?')) . ")";
            $sql_agent_params = $teamAgents;
        } else {
            // Get Personal Leads
            $sql_agent = " AND `u`.`agent` = ?";
            $sql_agent_params = array($authuser->info('id'));
        }
    }
}

    // Success
    $success = array();

    // Errors
    $errors = array();

    // DB connection
    $db = DB::get();

    // Maximum # of characters
    $maxlength = 160;

    // Media attachment
    $media = false;

    // Selected leads
    $leads = array();

try {
    // Available phone numbers
    $twilio = Partner_Twilio::getInstance();
    $numbers = $twilio->getTwilioNumbers();

    // First available phone number
    $numbers = array_slice($numbers, 0, 1);

    // REW Twilio API error
} catch (\Partner_Twilio_Exception $e) {
    $errors[] = $e->getMessage();
}

    // Send text message to selected leads
    $select_leads = $_POST['leads'] ?: $_GET['leads'];
    $select_leads = is_array($select_leads) ? $select_leads : explode(',', $select_leads);
if (!empty($select_leads)) {
    try {
        $params = array_merge($select_leads, $sql_agent_params);

        $query = $db->prepare("SELECT "
            . "`u`.`id`, `u`.`first_name`, `u`.`last_name`, `u`.`email`, `u`.`opt_texts`"
            . ", IFNULL(`tv`.`phone_number`, `u`.`phone_cell`) AS `phone_number`, `tv`.`optout`, `tv`.`verified`"
            . " FROM `users` `u`"
            . " LEFT JOIN `twilio_verified_user` `tvu` ON `tvu`.`user_id` = `u`.`id`"
            . " LEFT JOIN `twilio_verified` `tv` ON `tv`.`phone_number` = `tvu`.`phone_number`"
            . " WHERE `u`.`id` IN (" . implode(',', array_fill(0, count($select_leads), '?')) . ")"
            . ($isAgent ? $sql_agent : '')
            . " ORDER BY `tv`.`verified` DESC, `tv`.`optout` ASC, `tv`.`created_ts` DESC, `u`.`first_name` ASC, `u`.`last_name` ASC"
        . ";");

        $query->execute($params);
        while ($lead = $query->fetch()) {
            $leads[] = $lead;
        }
    } catch (PDOException $e) {
        $errors[] = 'Error occurred while loading selected leads.';
        //$errors[] = $e->getMessage();
    }
}

    // Send text message to search results
if (isset($_GET['text_search']) && isset($_SESSION['back_lead_search'])) {
    try {
        $params = array();
        if ($isAgent) {
            $params['agent'] = $authuser->info('id');
        }
        $query = $db->prepare("SELECT "
            . "`u`.`id`, `u`.`first_name`, `u`.`last_name`, `u`.`email`, `u`.`opt_texts`"
            . ", IFNULL(`tv`.`phone_number`, `u`.`phone_cell`) AS `phone_number`, `tv`.`optout`, `tv`.`verified`"
            . " FROM `users` `u`"
            . " LEFT JOIN `twilio_verified_user` `tvu` ON `tvu`.`user_id` = `u`.`id`"
            . " LEFT JOIN `twilio_verified` `tv` ON `tv`.`phone_number` = `tvu`.`phone_number`"
            . $_SESSION['back_lead_search']['sql_join']
            . " WHERE `u`.`id` IS NOT NULL"
            . (!empty($_SESSION['back_lead_search']['sql_where']) ? ' AND ' . $_SESSION['back_lead_search']['sql_where'] : '')
            . ($isAgent ? " AND `u`.`agent` = :agent" : '')
            . " ORDER BY `tv`.`verified` DESC, `tv`.`optout` ASC, `tv`.`created_ts` DESC, `u`.`first_name` ASC, `u`.`last_name` ASC"
        . ";");
        $query->execute($params);
        while ($lead = $query->fetch()) {
            $leads[] = $lead;
        }
    } catch (PDOException $e) {
        $errors[] = 'Error occurred while loading selected leads.';
        //$errors[] = $e->getMessage();
    }
}

    // Separate opt-in vs opt-out leads
    // Mass text messages can only be sent to verified phone numbers
    // or leads that have checked the box to opt-in to text messages
    // - and NEVER to a phone number that has explicitly OPT-OUT!
    $optin_leads = array();
    $optout_leads = array();
if (!empty($leads)) {
    foreach ($leads as $lead) {
        if (!empty($lead['phone_number'])
            && (!empty($lead['verified']) || $lead['opt_texts'] === 'in')
            && empty($lead['optout'])
        ) {
            $optin_leads[] = $lead;
        } else {
            $optout_leads[] = $lead;
        }
    }
}

    // Handle form submission
if (isset($_GET['submit']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check Errors
    if (empty($errors)) {
        try {
            // Media attachment URL
            $media = $_POST['media'] ?: null;
            if (!empty($media)) {
                $is_media_valid = parse_url($media);
                if (filter_var($media, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) === false) {
                    unset($media);
                    throw new UnexpectedValueException('Invalid media attachment.');
                }
            }

            // If no media, require a message body
            $message = Format::trim($_POST['body']);
            if (empty($media) && empty($message)) {
                throw new UnexpectedValueException('You must provide a message to send.');

                // Check message length
            } else if (strlen($message) > $maxlength) {
                throw new UnexpectedValueException('You cannot send a message longer than ' . $maxlength . ' characters.');
            }

            if (!isset($_POST['from'])) {
                // Available twilio numbers
                $twilio = Partner_Twilio::getInstance();
                $numbers = $twilio->getTwilioNumbers();

                // Send from first available phone number
                $numbers = array_slice($numbers, 0, 1);
                if (!empty($numbers)) {
                    $from = $numbers[0]['phone_number'];
                }
            } else {
                $from = $_POST['from'];
            }

            // Require valid number
            if (empty($from)) {
                throw new UnexpectedValueException('You must have a valid Twilio number.');
            }

            // Require some leads
            if (empty($leads)) {
                throw new UnexpectedValueException('You need to select some leads to text.');
            }

            // Send message to leads
            $lead_ids = array();
            foreach ($leads as $lead) {
                $lead_ids[] = $lead['id'];

                // Lead's phone number
                $to = $lead['phone_number'];

                // Generate message body
                $replace = array('{first_name}' => $lead['first_name'], '{last_name}' => $lead['last_name']);
                $body = str_replace(array_keys($replace), array_values($replace), $message);

                // Send text message to lead
                $twilio->sendSmsMessage($to, $from, $body, $media);

                // Success
                $success[] = 'Your text message has been sent.';

                // Track outgoing text message
                (new History_Event_Text_Outgoing(array(
                    'to'    => $to,
                    'from'  => $from,
                    'body'  => $body,
                    'media' => $media
                ), array(
                    new History_User_Lead($lead['id']),
                    $authuser->getHistoryUser()
                )))->save();
            }

            // Save Notices & Redirect to Form
            $authuser->setNotices($success, $errors);
            header('Location: ?leads=' . implode(',', $lead_ids) . '&success' . (isset($_GET['popup']) ? '&popup' : ''));
            exit;

            // REW Twilio error exception
        } catch (Partner_Twilio_Exception $e) {
            $errors[] = $e->getMessage();

            // Validation error has occurred
        } catch (UnexpectedValueException $e) {
            $errors[] = $e->getMessage();

            // Unexpected error
        } catch (Exception $e) {
            $errors[] = 'Something went wrong.';
            //$errors[] = $e->getMessage();
        }
    }
}
