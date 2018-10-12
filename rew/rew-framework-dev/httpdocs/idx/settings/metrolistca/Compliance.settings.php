<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">By searching you agree to the <a href="' . Settings::getInstance()->URLS['URL_IDX'] . 'terms-of-use.html">Terms of Use.</a></p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Metrolist Services Inc (California) Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/metrolist_cal_logo.jpg" border="0" style="float: left; margin: 0 15px 0 0;" />All measurements and all calculations of area (i.e., Sq Ft and Acreage) are approximate. Broker has represented to MetroList that Broker has a valid listing signed by seller authorizing placement in the MLS. Above information is provided by Seller and/or other sources and has not been verified by Broker. Copyright ' . date("Y") . ' MetroList Services, Inc. </p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Listings marked with <strong>V</strong>* indicate the seller is willing to entertain offers within a Listing Price range. For example, a Price of $140,000-$170,000 indicates the seller will entertain offers from $140,000 to $170,000. </p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Metrolist Services Inc (California) Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/metrolist_cal_logo.jpg" border="0" />';

$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/metrolist_cal_logo.jpg';
$_COMPLIANCE['logo_width'] = 10;   // Width (FPDF, Not Actual)
$_COMPLIANCE['logo_location'] = 2; // Placement

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}

// Terms of Use Page
$_COMPLIANCE['pages']['terms-of-use'] = array();
$_COMPLIANCE['pages']['terms-of-use']['page_title']     = 'Terms of Use Agreement';
$_COMPLIANCE['pages']['terms-of-use']['category_html']  = '<h1>Terms Of Use</h1>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<p>1. The following are terms of a legal agreement between you, the person viewing and/or using this Internet site ("User") and the operator of this Internet site ("Provider.") By accessing, browsing and/or using this Internet site ("Site"), User acknowledges that User has read, understood, and agrees to be bound by these terms and to comply with all applicable laws and regulations, including but not limited to U.S. export and re-export control laws and regulations. If User does not agree to these terms, User is not authorized to use this Site. The material provided on this Site is protected by law, including, but not limited to, United States Copyright law and international treaties. </p>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<p>2. User must be a prospective purchaser or seller of real estate with a bona fide interest in the purchase or sale of such real estate. </p>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<p>3. All real estate data provided on this Site is strictly for the personal, private, non-commercial use of User and is not made available for redistribution, retransmission, reformatting, modification or copying. User may not sell or use any of the real estate data on this Site for any purpose other than attempting to evaluate houses or properties for sale or purchase by User. User acknowledges that the real estate data on this Site is provided by MetroList Services, Inc., a California corporation ("MetroList"), and User acknowledges the validity of MetroList\'s copyright as to such data. User expressly acknowledges and agrees that MetroList is a third-party beneficiary of these Terms of Use, and that MetroList will be entitled to enforce these Terms of Use against User. </p>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<p>4. To the fullest extent permitted by law, the data on this Site is provided "as is," without warranty or representation of any kind, either express or implied, as to the nature, quality, characteristics or value of any property or information to which the data pertains. NEITHER PROVIDER NOR METROLIST MAKES ANY WARRANTIES, EXPRESS OR IMPLIED, REGARDING THE DATA DISPLAYED ON THIS SITE, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE, OR AS TO THE TIMELINESS, ACCURACY AND/OR COMPLETENESS OF THE DATA. </p>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<p>5. NEITHER PROVIDER NOR METROLIST SHALL BE LIABLE FOR ANY INCIDENTAL, SPECIAL, CONSEQUENTIAL, INDIRECT, SPECIAL, PUNITIVE OR EXEMPLARY DAMAGES WHATSOEVER (INCLUDING WITHOUT LIMITATION, DAMAGES FOR LOSS OF BUSINESS INFORMATION, LOSS OF DATA, LOST PROFITS, LOSS OF CUSTOMERS OR OTHER PECUNIARY LOSS), ARISING OUT OF THE USE OR INABILITY TO USE THE DATA DISPLAYED ON THIS SITE, WHETHER THE CLAIM OR CAUSE OF ACTION ARISES IN TORT, CONTRACT, NEGLIGENCE, STRICT LIABILITY OR UNDER ANY OTHER LEGAL THEORY. </p>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<p>6. User agrees that the prevailing party or parties in any action brought to enforce or for breach of these Terms of Use shall be entitled to recover, in addition to any other relief, that party\'s or those parties\' reasonable attorney\'s fees and court costs incurred in such action from the non-prevailing party or parties. </p>';
