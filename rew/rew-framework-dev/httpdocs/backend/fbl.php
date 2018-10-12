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
        exit;
    }

    /* Select Lead */
    $query   = "SELECT * FROM `" . LM_TABLE_LEADS . "` WHERE `email` = '".mysql_real_escape_string($_POST['address'])."';";
    $result = mysql_query($query);
    $lead   = mysql_fetch_array($result);

    if (empty($lead)) {
        header("HTTP/1.1 404 Not Found");
        echo 'No Lead Found';
        exit;
    }

    // Lead name
    $leadname = Format::trim($lead['first_name'] . ' ' . $lead['last_name']);

    $query = "SELECT * FROM `" . TABLE_AGENTS . "` WHERE `id` = '" . $lead['agent'] . "';";
    $result = mysql_query($query);
    $agent   = mysql_fetch_array($result);

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
    
    try {
        $event = new History_Event_Email_FBL(array(
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
        exit;
    }

    /* Opt-Out of Mailing Lists */
    mysql_query("UPDATE `" . LM_TABLE_LEADS . "` SET `fbl` = 'true' WHERE `id` = '" . mysql_real_escape_string($lead['id']) . "'");

    /* Email Subject */
    $mailer->Subject = "Lead Unsubscribed by FBL Detector";

    $mailer->Body  = "The following lead was unsubscribed because they reported an email you sent them as SPAM. The email report is attached."."\n\n";
    if (!empty($leadname)) {
        $mailer->Body .= "Name: " . $lead['first_name'] . ' ' . $lead['last_name'] . "\n";
    }
    $mailer->Body .= "Email: " . $lead['email'] . "\n";

    $mailer->AddStringAttachment($_POST['body'], null, '8bit', 'message/rfc822');

    $mailer->Send();

    echo 'Great Success!';
} else {
    header("HTTP/1.1 403 Forbidden");
    echo 'Required data missing';
    exit;
}
