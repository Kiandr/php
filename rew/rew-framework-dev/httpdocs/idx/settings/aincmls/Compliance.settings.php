<?php

global $_COMPLIANCE;
$_COMPLIANCE = array();

// MLS Disclaimer
$_COMPLIANCE['disclaimer'] = array();

// Listing Office on Details Page
if (in_array($_GET['load_page'], array('details','brochure'))) {
	$_COMPLIANCE['disclaimer'][] = '<p>Property Listed by: <?php global $listing; echo $listing[\'ListingOffice\']; ?></p>';
}

