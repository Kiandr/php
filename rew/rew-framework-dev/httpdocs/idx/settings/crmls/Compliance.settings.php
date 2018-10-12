<?php

// IDX Compliance Settings
global $_COMPLIANCE;
$_COMPLIANCE = array();

// MLS Disclaimer
$_COMPLIANCE['disclaimer'] = array();

// Only Show on Certain Pages
if (in_array($_GET['load_page'], array('details', 'map', 'streetview', 'birdseye', 'local', 'brochure', 'search', 'search_map', 'sitemap', '', 'dashboard'))) {

	// MLS Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Based on information from California Regional Multiple Listing Service, Inc.'
		. ' as of <?=date(\'F jS, Y \a\t g:ia T\', strtotime($last_updated)); ?>. This information is for your personal, non-commercial'
		. ' use and may not be used for any purpose other than to identify prospective properties you may be interested in purchasing.'
		. ' Display of MLS data is usually deemed reliable but is NOT guaranteed accurate by the MLS. Buyers are responsible for verifying'
		. ' the accuracy of all information and should investigate the data themselves or retain appropriate professionals. Information from'
		. ' sources other than the Listing Agent may have been included in the MLS data. Unless otherwise specified in writing, Broker/Agent'
		. ' has not and will not verify any information obtained from other sources. The Broker/Agent providing the information contained'
		. ' herein may or may not have been the Listing and/or Selling Agent.</p>';

}

// Listing Details: Display Agent Name
$_COMPLIANCE['details']['show_agent'] = ($_GET['load_page'] != 'map' && $_GET['page'] != 'cron') ? true : false;

// Listing Details: Display Office Name
$_COMPLIANCE['details']['show_office'] = ($_GET['load_page'] != 'map' && $_GET['page'] != 'cron') ? true : false;
