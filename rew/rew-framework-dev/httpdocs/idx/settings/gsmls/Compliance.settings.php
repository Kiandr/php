<?php

// Global Compliance
global $_COMPLIANCE;

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array();

if (in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', '', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The data relating to real estate for sale on this website comes in part from the IDX Program of Garden State Multiple Listing Service, L.L.C. Real estate listings held by other brokerage firms are marked as IDX Listing. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Information deemed reliable but not guaranteed. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright &copy; ' . date('Y') . ' Garden State Multiple Listing Service, L.L.C. All rights reserved. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Notice: The dissemination of listings on this website does not constitute the consent required by N.J.A.C. 11:5.6.1 (n) for the advertisement of listings exclusively for sale by another broker. Any such consent must be obtained in writing from the listing broker. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">This information is being provided for Consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties Consumers may be interested in purchasing. </p>';

}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));
