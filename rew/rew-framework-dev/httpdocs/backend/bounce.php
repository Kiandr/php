<?php

/* Send as PlainText */
header("Content-type: text/plain");

/* Set Page */
$_GET['page'] = 'server';

/* Include Common File */
require_once dirname(__FILE__) . '/common.inc.php';
@session_destroy();

$required_vars = true;
if (!isset($_POST['hash'])) {
    $required_vars = false;
}
if (!isset($_POST['address'])) {
    $required_vars = false;
}
if (!isset($_POST['body'])) {
    $required_vars = false;
}

if ($required_vars) {
    $hash = strtoupper(sha1('JJmIokGmrBcMSedw'.$_POST['address'].'wFmqCAAQAkOCmBMm'));

    /* Verify the integrity of the post */
    if ($hash !== $_POST['hash']) {
        header("HTTP/1.1 403 Forbidden");
        echo 'Hash mismatch';
        error_log('Hash mismatch');
        exit;
    }

    // Check if e-mail host is blocked
    if (Validate::verifyWhitelisted($_POST['address'])) {
        echo $lead['email'] . '\'s e-mail provider is on the server block list - skipping automated e-mail' . PHP_EOL;

        exit;
    }

    /* Select Lead */
    $query   = "SELECT * FROM `" . LM_TABLE_LEADS . "` WHERE `email` = '".mysql_real_escape_string($_POST['address'])."';";
    $result = mysql_query($query);
    $lead   = mysql_fetch_array($result);

    if (empty($lead)) {
        header("HTTP/1.1 404 Not Found");
        echo 'No Lead Found';
        error_log('No Lead Found - ' . $_POST['address']);
        exit;
    }

    // Lead name
    $leadname = Format::trim($lead['first_name'] . ' ' . $lead['last_name']);

    $query = "SELECT * FROM `" . TABLE_AGENTS . "` WHERE `id` = '" . $lead['agent'] . "';";
    $result = mysql_query($query);
    $agent   = mysql_fetch_array($result);

    if (strcasecmp($lead['email'], $agent['email']) == 0) {
        echo 'Agent and Lead email the same. Would cause a bounce loop.';
        error_log('Agent and Lead email the same - '.$lead['email']);
        return;
    }

    /**
     * Create Mailer
     */
    $mailer = new \PHPMailer\RewMailer();
    $mailer->IsHTML(false);
    $mailer->CharSet = 'UTF-8';

    /* Sender Details */
    $mailer->FromName = 'Lead Manager at ' . $_SERVER['HTTP_HOST'];
    $mailer->From     = Settings::getInstance()->SETTINGS['EMAIL_NOREPLY'];

    /* Send To */
    $mailer->AddAddress($agent['email'], $agent['first_name'] . ' ' . $agent['last_name']);

    preg_match_all('/^Subject: (.+)$/m', $_POST['body'], $match);
    $subject = end($match[1]);

    $bounce_subject = array(
                        'Undelivered Mail Returned to Sender',
                        'failure notice',
                        'Undeliverable.*',
                        'Delivery Status Notification \(Failure\)',
                        'Mail System Error - Returned Mail',
                        'Delivery Failure',
                        'There was an error sending your mail',
                        'Mail delivery failed.*',
                        'Delivery Notification: Delivery has failed',
                        'Returned mail: User unknown*',
                    );

    $bounce = false;
    foreach ($bounce_subject as $sbj) {
        if (preg_match('/'.$sbj.'/i', $match[1][0])) {
            $bounce = true;
            break;
        }
    }

    try {
        $event = new History_Event_Email_Bounce(array(
           'subject' => $subject,
           'message' => $_POST['body'],
           'plaintext' => true,
        ), array(
            new History_User_Lead($lead['id'])
        ));

        $event->save();
    } catch (Exception $e) {
        header("HTTP/1.1 500 Internal Server Error");
        echo $e->getMessage();
        error_log($e->getMessage());
        exit;
    }

    if ($bounce) {
        /* Opt-Out of Mailing Lists */
        mysql_query("UPDATE `" . LM_TABLE_LEADS . "` SET `bounced` = 'true' WHERE `id` = '" . mysql_real_escape_string($lead['id']) . "'");

        /* Email Subject */
        $mailer->Subject = "Lead Unsubscribed by Bounce Detector";

        $mailer->Body  = "The following lead was unsubscribed due to a bounced email. The bounced email is attached."."\n\n";
        if (!empty($leadname)) {
            $mailer->Body .= "Name: " . $lead['first_name'] . ' ' . $lead['last_name'] . "\n";
        }
        $mailer->Body .= "Email: " . $lead['email'] . "\n";
    } else {
        /* Email Subject */
        $mailer->Subject = "Email Received by Bounce Detector";

        $mailer->Body  = "An email from the following lead was received. The bounced email is attached."."\n\n";
        if (!empty($leadname)) {
            $mailer->Body .= "Name: " . $lead['first_name'] . ' ' . $lead['last_name'] . "\n";
        }
        $mailer->Body .= "Email: " . $lead['email'] . "\n";
    }

    $mailer->AddStringAttachment($_POST['body'], null, '8bit', 'message/rfc822');

    $mailer->Send();

    echo 'Great Success!';
} else {
    header("HTTP/1.1 403 Forbidden");
    echo 'Required data missing';
    error_log('Required data missing');
    exit;
}
