<?php

// User Session
$user = User_Session::get();

// IDX Navigation
$navigation = array(array(
    'title' => 'Search Tools',
    'pages' => array(
        array('link' => Settings::getInstance()->URLS['URL_IDX'], 'title' => 'Start Search'),
        array('link' => Settings::getInstance()->URLS['URL_IDX_MAP'], 'title' => 'Map Search'),
    )
));

// Logged In
if ($user->isValid()) {
    $navigation[0]['pages'][] = array('link' => '/idx/dashboard.html', 'title' => 'My Dashboard', 'class' => 'popup', 'data-popup' => '{"header":true}');
    $navigation[0]['pages'][] = array('link' => Settings::getInstance()->SETTINGS['URL_IDX_LOGOUT'], 'title' => 'Logout');

// Not Logged In
} else {
    $navigation[0]['pages'][] = array('link' => Settings::getInstance()->SETTINGS['URL_IDX_LOGIN'], 'title' => 'Login', 'class' => 'popup');
    $navigation[0]['pages'][] = array('link' => Settings::getInstance()->SETTINGS['URL_IDX_REGISTER'], 'title' => 'Register', 'class' => 'popup');
}
