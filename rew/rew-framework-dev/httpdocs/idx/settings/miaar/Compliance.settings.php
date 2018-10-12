<?php

// IDX Compliance Settings 
$_COMPLIANCE = array();

// Disclaimer Text 
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form','dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The data relating to real estate for sale on this web site comes from the participating brokers of the Marco Island Area Association of REALTORS&reg; Multiple Information Service.  The properties displayed may not be all of the properties in the MLS\'s database, or all of the properties listed with Brokers participating in the cooperative data exchange program.  Properties listed by Brokers other than this Broker are marked with either the listing Broker\'s logo or name or the MLS name or a logo provided by the MLS. Detailed information about such properties includes the name of the listing Broker.  Any use of search facilities of data on the site, other than a consumer looking to purchase real estate, is prohibited.  Information displayed on this web site is Deemed Reliable But Not Guaranteed.  You are advised to verify facts that are important to you.  No warranties, expressed or implied, are provided for the data herein, or for their use or interpretation by the user.  The Marco Island Area Association of REALTORS&reg; does not create, control or review the property data displayed herein and takes no responsibility for the content of such records. Federal law prohibits discrimination on the basis of race, color, religion, sex, handicap, familial status or national origin in the sale, rental or financing of housing.  Copyright ' . date('Y') . ' Marco Island Area Association of REALTORS&reg;. All rights reserved.</p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Marco Island Area Association Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/miaar.gif" border="0" />';

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('local', 'map', 'directions'));
