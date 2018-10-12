<?php

// Body Classes
if (isset($_GET['leads'])) {
    $body_class = 'dialer-line-selector';
} else {
    $body_class = 'dialer-line-manager';
}

// Get Authorization Managers
$partnersAuth = new REW\Backend\Auth\PartnersAuth(Settings::getInstance());

// Require Authorization
if (!$partnersAuth->canManageEspresso($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage espresso integrations')
    );
}

// Build API Object
$api = new Partner_Espresso();

$limit = (Settings::getInstance()->MODULES['REW_PARTNERS_ESPRESSO'] > 0) ? Settings::getInstance()->MODULES['REW_PARTNERS_ESPRESSO'] : 0;

// Make Sure Some Leads Are Being Requested
if (isset($_GET['leads'])) {
    // Grab the Contact IDs
    $contacts = explode(',', $_GET['leads']);
}

// REW Dialer (Espresso) line accounts are linked to the client's domain, We don't want to enable them for Dev Sites
$check_dev = Http_Host::getDev();
