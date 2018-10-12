<?php

if ($authuser->isValid()) {
    // Log Event: Agent Logged Out
    $event = new History_Event_Action_Logout(array(
        'ip' => $_SERVER['REMOTE_ADDR']
    ), array(
        $authuser->getHistoryUser()
    ));

    // Save to DB
    $event->save();

    // Log Out
    $authuser->logout();

    // Remote Date Filter
    unset($_SESSION['date']);
}

// Redirect
header('Location: ' . URL_BACKEND);
exit;
