<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form','dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright&copy; '.date("Y").' by the Multiple Listing Service of the Pensacola Association of REALTORS&reg;. This information is believed to be accurate but is not guaranteed. Subject to verification by all parties. This data is copyrighted and may not be transmitted, retransmitted, copied, framed, repurposed, or altered in any way for any other site, individual and/or purpose without the express written permission of the Multiple Listing Service of the Pensacola Association of REALTORS&reg;. Florida recognizes single and transaction agency relationships. Information Deemed Reliable But Not Guaranteed. Any use of search facilities of data on this site, other than by a consumer looking to purchase real estate, is prohibited. </p>';

}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map','local'));
