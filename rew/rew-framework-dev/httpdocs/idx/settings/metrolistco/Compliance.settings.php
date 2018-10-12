<?php

// IDX Compliance Settings
global $_COMPLIANCE;
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = !in_array($_GET['load_page'], array('streetview', 'birdseye', 'local')) ? true : false;

// MLS Disclaimer
$_COMPLIANCE['disclaimer'] = array('');

// Only Show on Certain Pages
if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'brochure', 'sitemap', 'dashboard'))) {

    // Require Broker Name
	$broker_name = '[INSERT BROKER NAME]';

    // Disclaimer Text
    $_COMPLIANCE['disclaimer'][] = '<p><img alt="Metrolist Services Inc (Colorado) Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/metrolist-co.png" border="0" style="float: left; margin: 10px; width: 100px;" /> <a href="' . Settings::getInstance()->URLS['URL_IDX'] . 'terms-and-conditions.html" title="Metrolist IDX Terms and Conditions">Terms and Conditions:</a> The content relating to real estate for sale in this Web site comes in  part from the Internet Data eXchange (&quot;IDX&quot;) program of METROLIST, INC., DBA RECOLORADO&reg; Real estate listings held by brokers other than ' . $broker_name . ' are marked with the IDX Logo. This information is being  provided for the consumers personal, non-commercial use and may not be  used for any other purpose. All information subject to change and  should be independently verified.</p>';
    $_COMPLIANCE['disclaimer'][] = '<p>Copyright ' . date("Y") . ' METROLIST, INC., DBA RECOLORADO&reg; -- All Rights Reserved 6455 S. Yosemite St., Suite 500  Greenwood Village, CO 80111 USA</p>';
    $_COMPLIANCE['disclaimer'][] = '<div itemscope itemtype="https://schema.org/Product"><meta itemprop="provider" content="RETAH" /><meta itemprop="owns" content="REcolorado" /></div>';

}

// Search Results: Display Thumbnail Icon
if (!in_array($_GET['load_page'], array('brochure'))) {
	$_COMPLIANCE['results']['lang']['provider'] = '<img alt="Metrolist Services Inc (Colorado) Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/metrolist-co-thumb.png" border="0" />';
	$_COMPLIANCE['details']['lang']['provider'] = '<img alt="Metrolist Services Inc (Colorado) Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/metrolist-co-thumb.png" border="0" />';
} else {
	$_COMPLIANCE['provider']['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/metrolist-co.png';
	$_COMPLIANCE['provider']['logo_width'] = 10;   // Width (FPDF, Not Actual)
}

// Search Results: Display MLS #
$_COMPLIANCE['results']['show_mls'] = true;

// Search Results: Display Office Name
$_COMPLIANCE['results']['show_office'] = true;
$_COMPLIANCE['featured']['show_office'] = true;
// Listing Details: Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'local')) ? true : false;

// Lang Provider
$_COMPLIANCE['details']['lang']['provider_bold'] = true;

// Gallery Compliance
$_COMPLIANCE['gallery']['show_provider'] = true;
$_COMPLIANCE['gallery']['show_disclaimer'] = true;

// Search Results: Display listing status
$_COMPLIANCE['results']['show_status'] = true;
$_COMPLIANCE['details']['show_status'] = true;

// Listing Details, Show Agent/Office Below Remarks
$_COMPLIANCE['details']['show_below_remarks'] = true;

// Show Office in Brochure Header
$_COMPLIANCE['details']['show_office_in_header'] = true;

// Listing Details, Remove Listing Heading
$_COMPLIANCE['details']['remove_heading'] = true;

// Listing Details, Show Provider Below Photos
$_COMPLIANCE['details']['show_below_photos'] = true;

// Disable tooltip on map pages
$_COMPLIANCE['local']['disable_popup'] = true;

// Brochure Logo
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/metrolist-co.png';
$_COMPLIANCE['logo_width'] = 17;   // Width (FPDF, Not Actual)
$_COMPLIANCE['logo_location'] = 1; // Placement

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}

// Terms and Conditions Page
$_COMPLIANCE['pages']['terms-and-conditions'] = array();
$_COMPLIANCE['pages']['terms-and-conditions']['page_title']     = 'Terms and Conditions';
$_COMPLIANCE['pages']['terms-and-conditions']['category_html']  = '<h1>Terms and Conditions</h1>';
$_COMPLIANCE['pages']['terms-and-conditions']['category_html']  .= '<ol>';
$_COMPLIANCE['pages']['terms-and-conditions']['category_html']  .= '<li>This publication is designed to provide information with regard to the subject matter covered. It is displayed with the understanding that the publisher and authors are not engaged in rendering real estate, legal, accounting, tax, or other professional services and that the publisher and authors are not offering such advice in this publication. If real estate, legal, or other expert assistance is required, the services of a competent, professional person should be sought.</li>';
$_COMPLIANCE['pages']['terms-and-conditions']['category_html']  .= '<li>The information contained in this publication is subject to change without notice. METROLIST, INC., DBA RECOLORADO&reg; MAKES NO WARRANTY OF ANY KIND WITH REGARD TO THIS MATERIAL, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE. METROLIST, INC., DBA RECOLORADO&reg; SHALL NOT BE LIABLE FOR ERRORS CONTAINED HEREIN OR FOR ANY DAMAGES IN CONNECTION WITH THE FURNISHING, PERFORMANCE, OR USE OF THIS MATERIAL.</li>';
$_COMPLIANCE['pages']['terms-and-conditions']['category_html']  .= '<li>PUBLISHER\'S NOTICE: All real estate advertised herein is subject to the Federal Fair Housing Act and the Colorado Fair Housing Act, which Acts make it illegal to make or publish any advertisement that indicates any preference, limitation, or discrimination based on race, color, religion, sex, handicap, familial status, or national origin.</li>';
$_COMPLIANCE['pages']['terms-and-conditions']['category_html']  .= '<li>METROLIST, INC., DBA RECOLORADO&reg; will not knowingly accept any advertising for real estate that is in violation of the law. All persons are hereby informed that all dwellings advertised are available on an equal opportunity basis.</li>';
$_COMPLIANCE['pages']['terms-and-conditions']['category_html']  .= '<li><img alt="Metrolist Services Inc (Colorado) Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/equal_house.jpg" /></li>';
$_COMPLIANCE['pages']['terms-and-conditions']['category_html']  .= '<li>&copy; ' . date("Y") . ' METROLIST, INC., DBA RECOLORADO&reg; &ndash; All Rights Reserved 6455 S. Yosemite St., Suite 500 Greenwood Village, CO 80111 USA</li>';
$_COMPLIANCE['pages']['terms-and-conditions']['category_html']  .= '<li>ALL RIGHTS RESERVED WORLDWIDE. No part of this publication may be reproduced, adapted, translated, stored in a retrieval system or transmitted in any form or by any means, electronic, mechanical, photocopying, recording, or otherwise, without the prior written permission of the publisher. The information contained herein including but not limited to all text, photographs, digital images, virtual tours, may be seeded and monitored for protection and tracking.</li>';
$_COMPLIANCE['pages']['terms-and-conditions']['category_html']  .= '</ol>';

// REcolorado Tracking
if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'birdseye', 'local'))) {
	$_COMPLIANCE['tracking']['REcolorado'] = array('ba397923-b92e-4d03-90e5-aaebcfdf7690', array('ListingMLS', 'AddressZipCode'));
}

