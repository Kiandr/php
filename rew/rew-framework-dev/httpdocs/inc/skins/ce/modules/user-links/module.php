<?php

// Get current user session
$user = User_Session::get();

$settings = Container::getInstance()->get(Settings::class);

$default_image = sprintf('%s/inc/skins/ce/img/person.png', $settings->URLS['URL']);
