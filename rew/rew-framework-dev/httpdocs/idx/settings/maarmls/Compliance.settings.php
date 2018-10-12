<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

// Require Broker Name
$broker_name = '[INSERT BROKER NAME]';

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {
	    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
		    $_COMPLIANCE['disclaimer'][] = 'Information herein is believed to be accurate and timely, but no warranty as such is expressed or implied. Listing information copyright "<?=date(\'Y\');?>" Multiple Listing Service, Inc. of Montgomery Area Association of REALTORS&reg;, Inc. The information being provided is for consumers\' personal, non-commercial use and will not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. The data relating to real estate for sale on this web site comes in part from the IDX Program of the Multiple Listing Service, Inc. of Montgomery Area Association of REALTORS&reg;. Real estate listings held by brokerage firms other than '.$broker_name.' are governed by MLS Rules and Regulations and detailed information about them includes the name of the listing companies.';
			    $_COMPLIANCE['disclaimer'][] = '</p>';
}

$_COMPLIANCE['results']['show_office'] = true;
$_COMPLIANCE['details']['show_office'] = true;

?>
