<?php

global $_COMPLIANCE;
$_COMPLIANCE = array();

// MLS Disclaimer
$_COMPLIANCE['disclaimer'] = array();

// Listing Office on Details Page
if (in_array($_GET['load_page'], array('details','brochure'))) {
	$_COMPLIANCE['disclaimer'][] = '<p>Property Listed by: <?php global $listing; echo $listing[\'ListingOffice\']; ?></p>';
}

// Disclaimer
$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The information being provided is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. </p>';
$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
$_COMPLIANCE['disclaimer'][] = 'Based on information from the Austin Board of REALTORS&reg; (alternatively, from ACTRIS) from <?=date(\'F jS, Y \\a\\t g:ia T\', strtotime($last_updated)); ?>. ';
$_COMPLIANCE['disclaimer'][] = 'Neither the Board nor ACTRIS guarantees or is in any way responsible for its accuracy. The Austin Board of REALTORS&reg;, ACTRIS and their affiliates provide the MLS and all content therein "AS IS" and without any warranty, express or implied. ';
$_COMPLIANCE['disclaimer'][] = 'Data maintained by the Board or ACTRIS may not reflect all real estate activity in the market. ';
$_COMPLIANCE['disclaimer'][] = '</p>';
$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">All information provided is deemed reliable but is not guaranteed and should be independently verified. </p>';

// Search Results Compliance
$_COMPLIANCE['results']['show_office'] = !empty(Settings::getInstance()->SETTINGS['registration']) ? true : false;

// Details Page Compliance
// Required if Forced Registration (VOW Requirements)
if (isset(Settings::getInstance()->SETTINGS['registration'])
		&& !empty(Settings::getInstance()->SETTINGS['registration'])
		&& !in_array($_GET['load_page'], array('local','map','details','brochure'))) {
	$_COMPLIANCE['details']['show_office'] = true;
	$_COMPLIANCE['details']['lang']['provider'] = "Property Listed By:";
}
