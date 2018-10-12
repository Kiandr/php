<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'map', 'streetview', 'birdseye', 'directions', 'local', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img src="'
		.Settings::getInstance()->SETTINGS['URL_IMG']
		.'logos/brea.gif" width="90px" style="width:90px;" />The data relating to'
		.' real estate for sale/lease on this website comes in part'
		.'from a cooperative data exchange program of the Bahamas Multiple'
		.'Listing Service (MLS) in which these Brokers participate (BREA). The'
		.'properties displayed may not be all of the properties in the Bahamas MLS'
		.'database, or all of the properties listed with Brokers participating in the'
		.'cooperative data exchange program. Properties listed by Brokers other than'
		.'this Broker/Agent are marked with the IDX Symbol, indicating an agreement'
		.'to exchange property information. Detailed information about such properties'
		.'are noted as Courtesy of: The Bahamas MLS. Information provided is'
		.'thought to be reliable but is not guaranteed to be accurate. You are advised to'
		.'verify facts that are important to you. No warranties, expressed or implied,'
		.'are provided for the data herein, or for their use or interpretation by the user.'
		.'This information is protected from unlawful duplication by copyright.</p>';

} else if (in_array($_GET['load_page'], array('details', 'brochure'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img src="'
		.Settings::getInstance()->SETTINGS['URL_IMG']
		.'logos/brea.gif" width="90px" style="width:90px;" />The Bahamas Real Estate'
		.' Association is not responsible for the accuracy of'
		.'the information listed above. The data relating to real estate for sale or lease'
		.'on this web site comes in part from the Internet Data Exchange Program (IDX)'
		.'and the Bahamas MLS, and is provided here for consumer\'s personal, noncommercial'
		.'use. It may not be used for any purpose other than to identify'
		.'prospective properties consumers may be interested in purchasing or leasing.'
		.'Real Estate listings held in brokerage firms other than the office owning this'
		.'website are marked with the IDX Logo on the short inquiry. Data provided is'
		.'deemed reliable but not guaranteed. This information is protected by copyright'
		.'and all rights are reserved. This property is shown courtesy of The Bahamas MLS.</p>';

}

// Results: show logo
$_COMPLIANCE['results']['show_icon'] = '<img src="'.Settings::getInstance()->SETTINGS['URL_IMG'].'logos/brea.gif" width="50px" style="width:50px;" />';

// Print Brochure: show logo
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'].'logos/brea.gif';
$_COMPLIANCE['logo_width'] = 15;   // Width (FPDF, Not Actual)
$_COMPLIANCE['logo_location'] = 1; // Placement
