<?php

// Global Compliance
global $_COMPLIANCE;
global $idx;

// IDX Compliance Settings
$_COMPLIANCE = array();

/* Display Update Time */
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'brochure', 'sitemap', 'dashboard')) || $force) {

	$_COMPLIANCE['disclaimer'][] = '<div class="disclaimer">';

	if ($idx->isCommingled()) {
		$_COMPLIANCE['disclaimer'][] = '<p><img alt="First Multiple Listing Service Inc Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/fmls.gif" alt="FMLS Logo" border="0" width="80" height="20" style="Float: left; margin-right: 8px;" /> Listings identified with the FMLS IDX logo come from FMLS, are held by brokerage firms other than the owner of this website and the listing brokerage is identified in any listing details. Information is deemed reliable but is not guaranteed.  If you believe any FMLS listing contains material that infringes your copyrighted work, please <a href="https://www.fmls.com/dmca.htm">click here</a> to review our DMCA policy and learn how to submit a takedown request. &copy; ' . date('Y') . ' First Multiple Listing Service, Inc.</p>';
	} else {
		$_COMPLIANCE['disclaimer'][] = '<p>Listings on this website come from the FMLS IDX Compilation and may be held by brokerage firms other than the owner of this website. The listing brokerage is identified in any listing details. Information is deemed reliable but is not guaranteed. If you believe any FMLS listing contains material that infringes your copyrighted work, please <a href="https://www.fmls.com/dmca.htm">click here</a> to review our DMCA policy and learn how to submit a takedown request. &copy; ' . date('Y') . ' FMLS</p>';
	}

	$_COMPLIANCE['disclaimer'][] = '<p>For issues regarding this website (and/or listing search engine, as applicable) please contact <a href="http://www.realestatewebmasters.com/" rel="nofollow" target="_blank">Real Estate Webmasters</a> - 250-753-9893</p>';
	$_COMPLIANCE['disclaimer'][] = '</div>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="First Multiple Listing Service Inc Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/fmls.gif" border="0" width="70" height="18" style="float: left; margin-right: 8px;" />';

$_COMPLIANCE['details']['above_inquire'] = true;

$_COMPLIANCE['details']['disclaimer_under_map'] = true;

$_COMPLIANCE['details']['remove_heading'] = true;

$_COMPLIANCE['details']['show_below_details'] = true;

$_COMPLIANCE['results']['show_immediately_below_listings'] = true;

$_COMPLIANCE['provider']['above_inquire'] = true;

if (in_array($_GET['load_page'], array('details', 'map', 'brochure'))) {
	$_COMPLIANCE['details']['show_office'] = true;
}

// Required if Forced Registration (VOW Requirements)
if (isset(Settings::getInstance()->SETTINGS['registration']) && !empty(Settings::getInstance()->SETTINGS['registration'])) {

	// Search Results, Display Office Name
	$_COMPLIANCE['results']['show_office'] = ($_GET['module'] != 'idx-dashboard' && !in_array($_GET['load_page'], array('dashboard')));

	if ($_GET['module'] != 'idx-dashboard' && !in_array($_GET['load_page'], array('dashboard'))) {
		// Prepend Provider
		$_COMPLIANCE['results']['lang']['provider'] = '<strong>Listed By:</strong><br>';
	}

} else {

	// Prepend Provider
	$_COMPLIANCE['results']['lang']['provider'] = ' ';

}

$_COMPLIANCE['dashboard']['show_mls'] = true;

$_COMPLIANCE['results']['show_mls'] = true;

$_COMPLIANCE['dashboard']['show_disclaimer_below_favorites'] = true;

$_COMPLIANCE['local']['disable_popup'] = true;

// Listing Details Provider
$_COMPLIANCE['details']['lang']['provider'] = '<strong>Listed By:</strong>';

if (in_array($_GET['load_page'], array('brochure'))) {

	$_COMPLIANCE['brochure']['align_office'] = 'C';

	// Listing Details Provider. The spaces are to center it.
	$_COMPLIANCE['details']['lang']['provider'] = 'Listed By:';

	$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/fmls.gif';
	$_COMPLIANCE['logo_width'] = 20; // Width (FPDF, not actual)
	$_COMPLIANCE['logo_location'] = 1; // Paragraph key
}
