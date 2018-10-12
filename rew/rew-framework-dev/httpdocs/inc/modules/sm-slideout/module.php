<?php

// User session
$user = User_Session::get();

// Social Networks
$networks = OAuth_Login::getProviders();
