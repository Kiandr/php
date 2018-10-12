<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

    // Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Based on information from the Multiple Listing Service of the HiCentral MLS&reg;, Ltd. active listings last updated on <?=date(\'F jS, Y \a\t g:ia T\', strtotime($last_updated)); ?>. Information is deemed reliable but not guaranteed. Copyright: ' . date('Y') . ' by the HiCentral MLS&reg;, Ltd.</p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Listing information is provided exclusively for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.</p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Some of the active listings appearing on this site may be listed by other REALTORS&reg;. If you are interested in those active listings, our company may represent you as the buyer\'s agent. If the active listing you are interested in is our company\'s active listing, you may speak to one of our agents regarding your options for representation.</p>';

}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));