<?php

// Require Composer Vendor Auto loader
require_once __DIR__ . '/../../boot/app.php';

// Console output
$_title = 'Running Encrypted Passwords Patch (' . basename(__FILE__) . ')';

// DB connection
$db = DB::get();

// Auth Instance
$user = User_Session::get();

// Get User Table Rows
$leads = $db->fetchAll("SELECT `id`, `email`, `password` FROM `users` WHERE `password` NOT LIKE '' AND `password` IS NOT NULL;");

// Begin Transaction
$db->beginTransaction();

// Output
echo PHP_EOL . 'Updating Users:' . PHP_EOL . PHP_EOL;

// Update featurd communities search criteria
if (!empty($leads)) {
    $update = $db->prepare("UPDATE `users` SET `password` = :password WHERE `id` = :id;");
    foreach ($leads as $lead) {

        //Get Encrypted Password  and Soap
        $encryptedPassword = $user->encryptPassword($lead['password']);

        // Update community
        if ($update->execute(array(
            'password' => $encryptedPassword,
            'id' => $lead['id']
        ))) {

            //Check Authentication
            if (!$user->authenticate($lead['email'], $lead['password'])) {
                //Rollback Transaction
                $db->rollBack();
                echo "\t" . 'Authentication Failed After Update.  Rolling Back.' . PHP_EOL;
                return;
            }

            // Output
            echo "\t" . '#' . $lead['id'] . ': '.PHP_EOL;
            echo "\tUsername:" . $lead['email'] . PHP_EOL;
            echo "\tPassword:" . $lead['password'] . PHP_EOL;
            echo "\tEncrypted Password:" . $encryptedPassword . PHP_EOL;

        } else {

            //Rollback Transaction
            $db->rollBack();
            echo "\t" . 'Database Update Failed.  Rolling Back.' . PHP_EOL;
            return;
        }
    }
} else {

    //Rollback Transaction
    $db->rollBack();
    echo "\t" . 'No Lead Entries Found.  Rolling Back.' . PHP_EOL;
    return;
}

// Commit the changes
$db->commit();

echo PHP_EOL;


// Output
echo PHP_EOL . 'Updating Password Reminder Autoresponder' . PHP_EOL . PHP_EOL;

// Update password reminder for the password reset feature.
$db->query("UPDATE `auto_responders` SET `document` = '<p>Hello {first_name},</p>\r\n<p>We\'ve received a request to reset the password for your account.</p>\r\n<p>To reset your password, click on the link below (or copy and paste the URL into your browser):\r\n</p><p><a href=\"{guid}\">{guid}</a>\r\n</p><p>If you didn\'t initiate this request, you don\'t need to take any further action and can safely disregard this email.\r\n</p><p>{signature}</p>' WHERE `id` = 8;");
