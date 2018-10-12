<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = 'The information being provided is for the consumer\'s personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumer may be interested in purchasing. Any information relating to real estate for sale referenced on this web site comes from the Internet Data Exchange (IDX) program of the Consolidated MLS&reg;. This web site may reference real estate listing(s) held by a brokerage firm other than the broker and/or agent who owns this web site. The accuracy of all information, regardless of source, including but not limited to square footages and lot sizes, is deemed reliable but not guaranteed and should be personally verified through personal inspection by and/or with the appropriate professionals.';
	$_COMPLIANCE['disclaimer'][] = '</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright, Consolidated MLS&reg;</p>';
}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;
