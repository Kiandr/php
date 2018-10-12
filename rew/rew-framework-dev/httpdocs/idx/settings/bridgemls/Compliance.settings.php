<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

    // Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<img alt="Bridge MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/combologo.jpg" border="0" style="float: left; margin: 0 15px 0.5em 0;" />  ';
    $_COMPLIANCE['disclaimer'][] = '<p>Bay East &copy;' . date('Y') . '. CCAR &copy;' . date('Y') . '. BridgeMLS &copy;' . date('Y') . '. Information deemed reliable but not guaranteed.<br />
	This information is being provided by the Bay East MLS or the CCAR MLS or the EBRDI MLS. The listings presented here may or may not be listed by the Broker/Agent operating this website.</p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Bridge MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/combologo.jpg" height="25" border="0" style="height: 25px;" />';

// Search Results, Display Agent Name
$_COMPLIANCE['results']['show_agent'] = true;

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = (in_array($_GET['load_page'], array('map', 'local'))) ? false : true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('map', 'local'))) ? false : true;

// Brochure Compliance
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/combologo.jpg';
$_COMPLIANCE['logo_width'] = 20; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(1, 2);
