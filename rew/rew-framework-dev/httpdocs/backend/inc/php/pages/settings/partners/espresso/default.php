<?php

// Full Page
$body_class = 'full';

// Get Authorization Managers
$partnersAuth = new REW\Backend\Auth\PartnersAuth(Settings::getInstance());

// Require Authorization
if (!$partnersAuth->canManageEspresso($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage espresso integrations')
    );
}

// Partner instance
$api = new Partner_Espresso();

// Account Limit
$accounts = (is_int(Settings::getInstance()->MODULES['REW_PARTNERS_ESPRESSO']) && Settings::getInstance()->MODULES['REW_PARTNERS_ESPRESSO'] > 0) ? Settings::getInstance()->MODULES['REW_PARTNERS_ESPRESSO'] : 0;

// Espresso line accounts are linked to a URL, We don't want to enable them for Dev Sites
$check_dev = Http_Host::getDev();
