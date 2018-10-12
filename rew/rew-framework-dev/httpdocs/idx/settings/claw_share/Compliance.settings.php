<?php

// IDX Compliance Settings
global $_COMPLIANCE;
$_COMPLIANCE = array();

// MLS Disclaimer
$_COMPLIANCE['disclaimer'] = array();

// Only Show on Certain Pages
if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// MLS Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The information being provided by CARETS is for the visitor\'s personal, noncommercial use and may not be used for any purpose other than to identify prospective properties visitor may be interested in purchasing. The data contained herein is copyrighted by CARETS, CLAW, CRISNet MLS, i-Tech MLS and/or VCRDS and is protected by all applicable copyright laws. Any dissemination of this information is in violation of copyright laws and is strictly prohibited. Any property information referenced on this web site comes from the Internet Data Exchange (IDX) program of CARETS. This web site may reference real estate listing(s) held by a brokerage firm other than the broker and/or agent who owns this web site. The accuracy of all information, regardless of source, is deemed reliable but not guaranteed and should be personally verified through personal inspection by and/or with the appropriate professionals.</p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Last updated on <?=date(\'F jS, Y \a\t g:ia T\', strtotime($last_updated)); ?></p>';

}

// Listing Details: Display Agent Name
$_COMPLIANCE['details']['show_agent'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Listing Details: Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));
