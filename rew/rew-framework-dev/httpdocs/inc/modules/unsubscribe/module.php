<?php

// CMS Database
$db = DB::get('cms');

// User Session
$user = User_Session::get();

// Valid User?
if ($user->isValid()) {
    $_GET['uid'] = Format::toGuid($user->info('guid'));
}

$_POST['uid'] = isset($_POST['uid']) ? trim($_POST['uid']) : trim($_GET['uid']);

// Select Row
if (!empty($_POST['uid'])) {
    if (Validate::guid($_POST['uid'])) {
        $check_email_lead = $db->fetch("SELECT * FROM `users` WHERE `guid` = GuidToBinary(" . $db->quote($_POST['uid']) . ") LIMIT 1;");
    } else if (Validate::sha1($_POST['uid'])) {
        $check_email_lead = $db->fetch("SELECT * FROM `users` WHERE SHA1(UPPER(`email`)) = " . $db->quote($_POST['uid']) . " LIMIT 1;");
    }
}

$data = json_decode(base64_decode($_REQUEST['d']), true);

// Unsubscribe Options
$options = array();
$options['opt_marketing'] = array('title' => 'Remove me from your marketing list and stop sending me newsletter emails.', 'selected' => ($check_email_lead['opt_marketing'] == 'out' ? true : false));
$options['opt_searches']  = array('title' => 'Unsubscribe from receiving listing updates matching my saved search criteria.', 'selected' => ($check_email_lead['opt_searches'] == 'out' ? true : false));
$unsubemail = Format::htmlspecialchars($_POST['unsubemail']);
// Lead ID was Provided
if (!empty($_POST['unsubscribe']) && is_array($_POST['unsubscribe']) && (!empty($unsubemail))) {
    // Validate Email
    try {
        $check_email = $db->fetch("SELECT * FROM `users` WHERE (`email` = :email);",
        [
            'email' => $unsubemail
        ]);

        $check_email_lead = $check_email;
    } catch (PDOException $e) {}

    // Validate Email Alternate
    if (empty($check_email)) {
        try {
            $check_email_alt = $db->fetch("SELECT * FROM `users` WHERE (`email_alt` = :email);",
                [
                    'email' => $unsubemail
                ]);

            $check_email_lead = $check_email_alt;
        } catch (PDOException $e) {}
    }

    // remove cc emails
    if (!empty($check_email_alt)) {
        // Opt-Out of CC Emails
        $db->query("UPDATE `users` SET `email_alt_cc_searches` = 'false' WHERE `id` = " . $db->quote($check_email_alt['id'] . ";"));
    }
    // Require Row
    if (!empty($check_email)) {
        // Opt-Out of Mailing List
        if (in_array('opt_marketing', $_POST['unsubscribe'])) {
            $db->query("UPDATE `users` SET `opt_marketing` = 'out' WHERE `id` = " . $db->quote($check_email_lead['id'] . ";"));
        }

        // Opt-Out of Saved Searches
        if (in_array('opt_searches', $_POST['unsubscribe'])) {
            $db->query("UPDATE `users` SET `opt_searches` = 'out' WHERE `id` = " . $db->quote($check_email_lead['id'] . ";"));
        }

        // Log Event: Lead Unsubscribed
        $event = new History_Event_Action_Unsubscribe(array(
            'unsubscribe' => $_POST['unsubscribe']
        ), array(
            new History_User_Lead($check_email_lead['id'])
        ));

        // Save to DB
        $event->save($db);

        // Select Lead Agent
        $agent = !empty($check_email_lead['agent']) ? $check_email_lead['agent'] : 1;
        $agent = $db->fetch("SELECT * FROM `agents` WHERE `id` = " . $db->quote($agent) . ";");

        // Select Super Admin
        if ($agent['id'] != 1) {
            $admin = $db->fetch("SELECT * FROM `agents` WHERE `id` = '1';");
        }

        // Send Opt-Out Notifications
        if (!empty($agent) || !empty($admin)) {
            // PHP Mailer Object
            $mailer = new \PHPMailer\RewMailer();

            // Sender
            $mailer->FromName = 'Lead Manager at ' . $_SERVER['HTTP_HOST'];
            $mailer->From     = Settings::getInstance()->SETTINGS['EMAIL_NOREPLY'];

            // Recipients
            if (!empty($admin)) {
                $mailer->AddAddress($admin['email'], $admin['first_name'] . ' ' . $admin['last_name']);
            }
            if (!empty($agent)) {
                $mailer->AddAddress($agent['email'], $agent['first_name'] . ' ' . $agent['last_name']);
            }

            // Message Subject
            $mailer->Subject  = 'Lead has Unsubscribed!';

            // Message Body
            $mailer->Body  = 'Hello ' . $agent['first_name'] . ' ' . $agent['last_name'] . "\n\n";
            $mailer->Body .= 'The following lead has decided to unsubscribe from the following: ' . "\n\n";
            if (!empty($_POST['unsubscribe'])) {
                foreach ($_POST['unsubscribe'] as $option) {
                    $mailer->Body .= ' ** ' . $options[$option]['title'] . "\n";
                }
            }
            $mailer->Body .= '<========================>' . "\n\n";
            $mailer->Body .= 'Name: ' . $check_email_lead['first_name'] . ' ' . $check_email_lead['last_name'] . "\n";
            $mailer->Body .= 'Email: ' . $check_email_lead['email'] . "\n";
            if (!empty($check_email_lead['phone'])) {
                $mailer->Body .= 'Phone: ' . $check_email_lead['phone'] . "\n";
            }
            if (!empty($check_email_lead['status'])) {
                $mailer->Body .= 'Status: ' . $check_email_lead['status'] . "\n";
            }
            if (!empty($check_email_lead['comments'])) {
                $mailer->Body .= 'Users Comments: ' . strip_tags($check_email_lead['comments']) . "\n";
            }
            if (!empty($check_email_lead['remarks'])) {
                $mailer->Body .= 'Remarks: ' . $check_email_lead['remarks'] . "\n";
            }
            $mailer->Body .= "\n";
            $mailer->Body .= '<========================>' . "\n\n";
            $mailer->Body .= 'Have a nice day!' . "\n";

            // Send Email
            $mailer->Send();
        }
    }
        elseif (empty($check_email)) {
            $errors[] = 'The e-mail entered was not found in the system.';
        }

}

// Re-Subscribe
if (!empty($_POST['uid']) && !empty($_POST['subscribe'])) {
    // Require Row
    if (!empty($check_email_lead)) {
        // Verify lead
        $db->query("UPDATE `users` SET `verified` = 'yes' WHERE `id` = " . $db->quote($check_email_lead['id']) . ";");

        /* Log Event: Lead has Verified Email Address */
        $event = new History_Event_Action_Verified(null, array(
                new History_User_Lead($check_email_lead['id'])
        ));

        /* Save to DB */
        $event->save();

        // Opt-in to Mailing List
        if (isset($_POST['opt_marketing'])) {
            $db->query("UPDATE `users` SET `opt_marketing` = 'in' WHERE `id` = " . $db->quote($check_email_lead['id']) . ";");
        }

        // Opt-in tp Saved Searches
        if (isset($_POST['opt_searches'])) {
            $db->query("UPDATE `users` SET `opt_searches` = 'in' WHERE `id` = " . $db->quote($check_email_lead['id']) . ";");
        }

        if (!empty($_POST['search']) && is_array($_POST['search'])) {
            foreach ($_POST['search'] as $search) {
                $db->query("UPDATE `users_searches` SET `frequency` = 'weekly' WHERE `id` = " . $db->quote($search) . ";");
            }
        }
    }
}
