<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

$_COMPLIANCE['limit'] = 100;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {


	if (in_array($_GET['load_page'], array('brochure'))) {
		$_COMPLIANCE['disclaimer'][] = '<span style="text-align:center;"><p class="disclaimer" >Listing Courtesy of Gulf South Real Estate Information Network</p></span>';
	}
	$_COMPLIANCE['disclaimer'][] = '<span style="text-align:center;"><p class="disclaimer" >'.
		'<img width="100" height="22" border="0" alt="Broker Reciprocity Logo" src="'.
		Settings::getInstance()->SETTINGS['URL_IMG'].'/logos/br.jpg" /><br />'.
		'Copyright <?=date(\'Y\');?>, New Orleans Metropolitan Association of REALTORS&reg;,'.
		' Inc. All rights reserved. Information deemed reliable, but not verified or guaranteed. Users are responsible for checking '.
		'the accuracy, completeness, currency, and status of all information.</p>';
	if (!in_array($_GET['load_page'], array('brochure'))) {
		$_COMPLIANCE['disclaimer'][] = '<p><a href="javascript:void(0);" onclick="$(\'#idx-terms-and-conditions\').toggle();">'.
			'Terms and Conditions</a></p><div id="idx-terms-and-conditions" style="display:none;"><h3>TERMS AND CONDITIONS</h3><p>Copyright '.
			'<?=date(\'Y\');?> New Orleans Metropolitan Association of REALTORS&reg;, Inc. All rights reserved. The sharing of MLS database,'.
			' or any portion thereof, with any unauthorized third party is strictly prohibited.</p><p>Information contained on this site is'.
			' believed to be reliable, yet, users of this web site are responsible for checking the accuracy, completeness, currency, or '.
			'suitability of all information. Neither the New Orleans Metropolitan Association of REALTORS&reg;, Inc. nor the Gulf South Real'.
			' Estate Information Network, Inc. makes any representation, guarantees, or warranties as to the accuracy, completeness, currency,'.
			' or suitability of the information provided. They specifically disclaim any and all liability for all claims or damages that may '.
			'result from providing information to be used on the web site, or the information which it contains, including any web sites maintained '.
			'by third parties, which may be linked to this web site.</p><p>The information being provided is for the consumer\'s personal, '.
			'non-commercial use, and may not be used for any purpose other than to identify prospective properties which consumers may be '.
			'interested in purchasing. The user of this site is granted permission to copy a reasonable, and limited number of copies to be '.
			'used in satisfying the purposes identified in the preceding sentence.</p><p>By using this site, you signify your agreement with '.
			'and acceptance of these terms and conditions. If you do not accept this policy, please do not use this site in any way. Your '.
			'continued use of this site, and/or its affiliates\' sites, following the posting of changes to these terms will mean you accept '.
			'those changes, regardless of whether you are provided with additional notice of such changes.</p></div>';
	}
	$_COMPLIANCE['disclaimer'][] = '</span>';
}

$_COMPLIANCE['results']['show_icon'] = '<img border="0" alt="Broker Reciprocity Logo" src="'.
		Settings::getInstance()->SETTINGS['URL_IMG'].'/logos/br-house-32.png" />';

$_COMPLIANCE['results']['provider_first'] = function($listing) { return true; };

$_COMPLIANCE['details']['extra_remarks'] = '<span style="text-align:center;"><p class="disclaimer" >Listing Courtesy of Gulf South Real Estate Information Network</p></span>';

$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br.jpg';
$_COMPLIANCE['logo_width'] = 25;   // Width (FPDF, Not Actual)
$_COMPLIANCE['logo_location'] = 1; // Placement
$_COMPLIANCE['shift_paragraphs'] = array(1, 2);
