<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Global Account Row
global $account;

// Require Broker Name
$account['broker_name'] = !empty($account['broker_name']) ? $account['broker_name'] : '[INSERT BROKER NAME]';
$vendor_name = 'Real Estate Webmasters';

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');
$_COMPLIANCE['disclaimer'][] = '<div style="text-align: center;">';
$_COMPLIANCE['disclaimer'][] = '<p>By searching, you agree to the <a href="' . Settings::getInstance()->URLS['URL_IDX'] . 'terms-of-use.html">End User License Agreement</a> </p>';
$_COMPLIANCE['disclaimer'][] = '<p>Information is supplied by seller and other third parties and has not been verified.</p>';
$_COMPLIANCE['disclaimer'][] = '<p>Copyright '.date('Y').' – Duluth Area Association of REALTORS&reg; MLS – All Rights Reserved.</p>';
$_COMPLIANCE['disclaimer'][] = '</div>';

$_COMPLIANCE['pages']['terms-of-use'] = array();
$_COMPLIANCE['pages']['terms-of-use']['page_title']     = 'Duluth Area Association of REALTORS&reg; MLS End User License Agreement';

$_COMPLIANCE['pages']['terms-of-use']['category_html']  = '<p>IDX information is provided by Duluth Area Association of REALTORS&reg; exclusively for consumers\' personal, non-commercial use, and it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. Data is deemed reliable but is not guaranteed accurate by the MLS. </p>';
$_COMPLIANCE['pages']['terms-of-use']['category_html']  .= '<p>Copyright '.date('Y').' – Duluth Area Association of REALTORS&reg; MLS – All Rights Reserved. </p>';

// Listing Details, Display Office Name
if(in_array($_GET['load_page'], array('details', 'brochure')))
{
	$_COMPLIANCE['details']['show_office'] = true;
}

//Compliance - 100 listings max
$_COMPLIANCE['limit'] = 100;
