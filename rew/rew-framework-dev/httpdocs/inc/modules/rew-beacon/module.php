<?php

// AJAX Beacon Request
if ($this->config('ajax')) {
    // User Session
    $user = User_Session::get();
    if (!empty($user) && $user->isValid()) {
        // DB Connection
        $db = DB::get('users');

        // Update User's Timestamp
        $update = $db->prepare("UPDATE `users` SET `timestamp_active` = NOW() WHERE `id` = :user_id;");
        $update->execute(array('user_id' => $user->user_id()));
    }

    // Exit
    exit;
}
