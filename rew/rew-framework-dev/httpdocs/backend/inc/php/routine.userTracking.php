<?php

@session_start(); // Start the session if none exists yet.

if (($_GET['id'] == '404' && $_SERVER['PHP_SELF'] == '/index.php')) {
    $userTrack_badPage = true; // Mark this view as non-tracked
}

if ($userTrack_badPage !== true) {
    // DB Connection
    $db = DB::get();

    // Tracking cookie
    $cookie = $_COOKIE[User_Visit::COOKIE_NAME];

    // Load Visit from Session
    $visit = $_SESSION[User_Visit::COOKIE_NAME];
    $visit = !empty($visit) && ($visit instanceof User_Visit) ? $visit : false;

    // Create New Visit
    if (empty($visit)) {
        // If the usertrack object is not yet created, make one
        $visit = new User_Visit($db);
        if (!empty($cookie) && $userTrackID < 1) {
            // If the user has a id cookie and the current page did not set an ID set the user_id
            if ($visit->authenticateUserTrack($cookie)) {
                $userTrackID = $visit->getUserID();
            }
        }
    }

    // Set Database
    $visit->setDB($db);

    // Track User ID
    if (!empty($userTrackID)) {
        if ($userTrackID != $visit->getUserID()) {
            $visit->setUserID($userTrackID);
        }
    } else {
        $userTrackID = $visit->getUserID();
        if (empty($userTrackID)) {
            $visit->authenticateUserTrack($cookie);
        }
    }

    // Track Page
    $visit->recordPage();

    // Store Visit in Session
    $_SESSION[User_Visit::COOKIE_NAME] = $visit;
}
