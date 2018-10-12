<?php

// Check if not logged in..
$user = User_Session::get();
if (!$user->isValid()) {
    // Get available social networks to connect
    $networks = OAuth_Login::getProviders();
}
