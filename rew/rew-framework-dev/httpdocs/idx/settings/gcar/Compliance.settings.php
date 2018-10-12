<?php

// IDX Compliance Settings
global $_COMPLIANCE;
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

// Require Broker Name
$broker_name = '[INSERT BROKER NAME]';

// Disclaimer
if (in_array($_GET['load_page'], array('details','brochure'))) {
	        $_COMPLIANCE['disclaimer'][] = '<div style="text-align:center;"><p>Listing Courtesy Of: <?=$listing["ListingOffice"];?></p></div>';
}
$_COMPLIANCE['disclaimer'][] = '<img alt="Greater Chattanooga Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/gcar_logo-med.jpg" border="0" />';
$_COMPLIANCE['disclaimer'][] = '<p>The information being provided is for consumers\' personal, noncommercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. </p>';
$_COMPLIANCE['disclaimer'][] = '<p>' . $broker_name . ' does not display the entire MLS of Chattanooga, Inc. database on this website. The listings of some real estate brokerage firms have been excluded. </p>';
$_COMPLIANCE['disclaimer'][] = '<p>Copyright&copy; <?=date(\' Y \'); ?> by Chattanooga Association of REALTORS&reg; </p>';
$_COMPLIANCE['disclaimer'][] = '<p>This site does not contain all listings available through the MLS. </p>';
$_COMPLIANCE['disclaimer'][] = '<p>INFORMATION DEEMED RELIABLE BUT NOT GUARANTEED. </p>';
$_COMPLIANCE['disclaimer'][] = '<p>This site was last updated on <?=date(\'F jS, Y \a\t g:ia\', strtotime($last_updated)); ?></p>';

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Greater Chattanooga Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/gcar_logo-sm.jpg" border="0" />';

$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/gcar_logo-med.jpg';
$_COMPLIANCE['logo_width'] = 34;   // Width (FPDF, Not Actual)
$_COMPLIANCE['logo_location'] = 1; // Placement
