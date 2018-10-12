<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = false;

// Require Broker Name
$broker_name = '[INSERT BROKER NAME]';

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
$_COMPLIANCE['disclaimer'][] = 'The data relating to real estate for sale on this website comes in part from the Broker Reciprocity Program of the NORIS MLS. ';
$_COMPLIANCE['disclaimer'][] = 'Real estate listings held by brokerage firms other than ' . $broker_name . ' are marked with the Broker Reciprocity logo or the Broker Reciprocity thumbnail logo (a little black house) and detailed information about them includes the name of the listing brokers. ';
$_COMPLIANCE['disclaimer'][] = 'Information is deemed reliable but is not guaranteed accurate by the MLS or ' . $broker_name . '. ';
$_COMPLIANCE['disclaimer'][] = 'Broker Reciprocity information is provided exclusively for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. ';
$_COMPLIANCE['disclaimer'][] = 'Data updated daily. Data last updated: ' . date('m/d/Y') . '. ' . PHP_EOL . PHP_EOL;
$_COMPLIANCE['disclaimer'][] = '</p>';

if (in_array($_GET['load_page'], array('details','brochure'))) {
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright ' . date('Y') . ' NORIS. All rights reserved. </p>';
}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_icon'] = '<img alt="Northwest Ohio Real Estate Information Systems Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br-house-32.png" style="width: 32px;">';

// Listing Details, Display Office Name
if (in_array($_GET['load_page'], array('details','brochure'))) {
    $_COMPLIANCE['details']['show_office'] = true;
}
