<?php
// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. 'Copyright ' . date('Y') . ' NWLAR. All rights reserved. The sharing of MLS database, or any'
		. ' portion thereof, with any unauthorized third party is strictly restricted.'
		. '</p>' . PHP_EOL . PHP_EOL;

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. 'Information contained on this site is believed to be reliable, yet, users of this web'
		. ' site are responsible for checking the accuracy, completeness, currency, or suitability of'
		. ' all information. Neither NWLAR nor M.L.S. Inc. makes any representation, guarantees,'
		. ' of information provided. They specifically disclaim any and all liability for all claims or'
		. ' damages that may result from providing information to be used on the website, or the'
		. ' information which it contains, including any websites maintained by third parties, which'
		. ' may be linked to this website.</p>' . PHP_EOL . PHP_EOL;

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. 'The information being provided is for the consumer\'s personal, non-commercial use,'
		. ' and may not be for any purpose other than to identify prospective properties which'
		. ' consumers may be interested in purchasing. The user of this site is granted permission to'
		. ' copy a reasonable and limited number of copies to be used in satisfying the purposes'
		. ' identified in the preceding sentence.</p>' . PHP_EOL . PHP_EOL;

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. 'By using this site you signify your agreement with and acceptance of these terms and'
		. ' conditions. If you do not accept this policy, please do not use this site in any way. Your'
		. ' continued use of this site, and/or its affiliates\' sites, following the posting of changes to'
		. ' these terms will mean you accept those changes, regardless of whether you are provided'
		. ' with additional notices of changes.</p>' . PHP_EOL . PHP_EOL;

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. 'Current as of <?=date(\'F jS, Y \a\t g:ia T\', strtotime($last_updated)); ?>.</p>';
}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

$_COMPLIANCE['results']['lang']['provider'] = 'Listing courtesy of ';
