<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Search Result Limit
$_COMPLIANCE['limit'] = 500;

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Require Broker Name
	$broker_name = '[INSERT BROKER NAME]';

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = 'DISCLAIMER: The information displayed on this page is confidential, proprietary, and copyrighted information of Heartland Multiple Listing Service, Inc. ("Heartland MLS").<br />';
	$_COMPLIANCE['disclaimer'][] = 'Copyright ' . date("Y") . ', Heartland Multiple Listing Service, Inc. Heartland MLS and ' . $broker_name . ' do not make any warranty or representation concerning the timeliness or accuracy of the information displayed herein. In consideration for the receipt of the information on this page, the recipient agrees to use the information solely for the private non-commercial purpose of identifying a property in which the recipient has a good faith interest in acquiring.<br />';
	$_COMPLIANCE['disclaimer'][] = 'By searching, you agree to the <a href="' . Settings::getInstance()->URLS['URL_IDX'] . 'terms-of-use.html">Terms of Use</a>.';
	$_COMPLIANCE['disclaimer'][] = '</p>';

}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details'));

// terms-of-use.html
$_COMPLIANCE['pages']['terms-of-use'] = array();
$_COMPLIANCE['pages']['terms-of-use']['page_title']     = 'Terms of Use Agreement';
$_COMPLIANCE['pages']['terms-of-use']['category_html']  = '<h1>Terms Of Use Agreement</h1>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<p>As consideration for the right to search this website operated by David Costello for properties for sale or lease, I/We ("User") agree to the following terms and conditions:</p>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<p>1. All data displayed on this website about properties for sale or lease are the proprietary asset of the Heartland Multiple Listing Service, Inc. ("HMLS"). HMLS owns all Intellectual Property Rights in and to such data, or the compilation of such data, including all copyright, patent, trade secret, or trademark rights. User will not contest HMLS\' Intellectual Property Right claims, nor assist others in doing so. User agrees that in the event User infringes upon HMLS\' Intellectual Property Rights in the property data displayed on this website, HMLS\' remedies at law are inadequate, and that HMLS is entitled to temporary and permanent injunctive relief to prohibit such an infringement.</p>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<p>2. User agrees that User is a bona fide potential purchaser or lessee of property in the market area served by HMLS and Reed Brinton. User agrees that any search performed by User of properties available for sale or lease utilizing this website is solely for the purpose of identifying properties in which User has legitimate and bona fide interest in buying or leasing.</p>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<p>3. User agrees that User shall not use this website, or any of the property data accessible on this website, for any commercial or business purpose. User shall not sell, loan, exchange, or license, or otherwise retransmit the property data accessible on this website to any third party without the express written permission of HMLS and Reed Brinton.</p>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<p>4. User waives any and all claims for damages of any kind against HMLS or Reed Brinton by reason of Users\' use of this website, or the property data available at this website, including, but not limited to actual, punitive, special, or consequential damages, or lost profits or unrealized savings.</p>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<p>5. This Agreement shall be governed by the laws of Kansas. User agrees that any suit to enforce this Agreement shall be brought in the state or federal courts having jurisdiction over Johnson County, Kansas. User waives any objection to the personal jurisdiction of such courts.</p>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<p>6. In the event a judgment is entered against User in any action by or against User to enforce the parties\' obligations under this Agreement, User shall pay the prevailing parties\' attorneys fees and costs incurred in the prosecution or defense of such action.</p>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<p>7. User acknowledges and agrees that HMLS is a third party beneficiary of this Agreement and both Reed Brinton and HMLS shall each have the authority to enforce its terms against User.</p>';

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}
