<?php

use REW\Backend\Partner\Inrix\DriveTime;
use REW\Core\Interfaces\SettingsInterface;

$container = Container::getInstance();
$drive_time = $container->get(DriveTime::class);
$settings = $container->get(SettingsInterface::class);

// Show form?
$show_form = true;

// Success Collection
$success = array();

// Error Collection
$errors = array();

// Form submitted
if (isset($_GET['submit'])) {
    // Required fields
    $required   = array();
    $required[] = array('value' => 'subject', 'title' => 'Subject');
    $required[] = array('value' => 'name',    'title' => 'Name');
    $required[] = array('value' => 'email',   'title' => 'E-Mail');
    $required[] = array('value' => 'phone',   'title' => 'Phone');
    $required[] = array('value' => 'inquiry', 'title' => 'Inquiry');

    // Process required fields
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = $require['title'] . ' is a required field.';
        }
    }

    // Check errors
    if (empty($errors)) {
        // Create \PHPMailer\RewMailer
        $mail = new \PHPMailer\RewMailer();
        $mail->CharSet = 'UTF-8';
        $mail->IsHTML(true);

        // Sender
        $mail->Sender   = $settings->SETTINGS['EMAIL_NOREPLY'];
        $mail->From     = $_POST['email'];
        $mail->FromName = $_POST['name'];

        // Reply To
        $mail->AddReplyTo($_POST['email']);

        // Recipient
        if ($authuser->isSuperAdmin()) {
            // Send to Support
            $mail->AddAddress('support@realestatewebmasters.com', 'REW Support');
        } else {
            // Send to Super Admin
            $agent = mysql_fetch_assoc(mysql_query("SELECT * FROM `agents` WHERE `id` = 1;"));
            if (!empty($agent)) {
                $mail->AddAddress($agent['email'], $agent['first_name'] . ' ' . $agent['last_name']);
            }
        }

        // E-mail Subject
        $mail->Subject = htmlspecialchars_decode('[' . ($authuser->is_super_admin() ? 'REW' : 'Agent') . ' Help Request] ' . $_POST['subject'] . ' (' . $settings->URLS['URL'] . ')');

        // E-mail Body
        $mail->Body = '';

        // Extra message for agent requests
        if (!$authuser->is_super_admin()) {
            $mail->Body .= '<p>An agent on your site has requested your assistance.</p>';
        }

        $mail->Body .= '<strong>Name:</strong> ' . htmlspecialchars($_POST['name']) . '<br/>';
        $mail->Body .= '<strong>Website:</strong> <a href="' . $settings->URLS['URL'] . '">' . $settings->URLS['URL'] . '</a><br/>';
        $mail->Body .= '<strong>User Agent:</strong> ' . htmlspecialchars($_SERVER['HTTP_USER_AGENT']) . '<br/>';
        $mail->Body .= '<strong>Phone #:</strong> ' . htmlspecialchars($_POST['phone']) . '<br/>';
        $mail->Body .= '<strong>Inquiry:</strong>';
        $mail->Body .= '<p>' . nl2br(htmlspecialchars($_POST['inquiry'])) . '</p>';

        // Send E-Mail
        if ($mail->Send()) {
            // Don't show form
            $show_form = false;
        } else {
            // Failed to send
            $errors[] = 'There was an error with your inquiry submission. Please try again later.';
        }
    }
} else {
    // Set defaults
    $_POST['name'] = $authuser->info('first_name') . ' ' . $authuser->info('last_name');
    $_POST['email'] = $authuser->info('email');
    $_POST['phone'] = $authuser->info('cell_phone');
}
