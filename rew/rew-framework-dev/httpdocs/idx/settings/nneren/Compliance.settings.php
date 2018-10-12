<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array();

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'dashboard'))) {

    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
    $_COMPLIANCE['disclaimer'][] = '<img alt="Northern New England Real Estate Network Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/nnerenlogo.png" border="0" style="float: left; margin: 15px 10px 5px 0px;" />';
    $_COMPLIANCE['disclaimer'][] = 'Copyright ' . date('Y') . ' Northern New England Real Estate Network, Inc.  All rights reserved. ';
    $_COMPLIANCE['disclaimer'][] = 'This information is deemed reliable but not guaranteed.<br />';
    $_COMPLIANCE['disclaimer'][] = 'The data relating to real estate for sale on this web site comes in part from the IDX Program of NNEREN.';
    $_COMPLIANCE['disclaimer'][] = '</p>';

}

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = (in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'local'))) ? false : true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'local'))) ? false : true;

$_COMPLIANCE['details']['show_below_remarks'] = true;

// Brochure logo
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/nnerenlogo.png';	// Path
$_COMPLIANCE['logo_width'] = 55; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(2);
