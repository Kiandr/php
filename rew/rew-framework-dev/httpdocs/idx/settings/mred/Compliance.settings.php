<?php

// Global Compliance
global $_COMPLIANCE;

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form','dashboard'))) {

	$broker_name = '[INSERT BROKER NAME]';

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Midwest Real Estate Data LLC Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br.jpg" border="0" style="float: left; margin: 5px 5px 5px 0px;" /> The data relating to real estate for sale on this website comes in part from the Broker Reciprocity program of Midwest Real Estate Data LLC. Real Estate listings held by brokerage firms other than ' . $broker_name . ' are marked with the Broker Reciprocity logo or the Broker Reciprocity thumbnail logo (a little black house) and detailed information about them includes the names of the listing brokers. Some properties which appear for sale on this website may subsequently have sold and may no longer be available. The information being provided is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. Information deemed reliable but not guaranteed. Many homes contain recording devices, and buyers should be aware they may be recorded during a showing. </p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Midwest Real Estate Data LLC Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br-house.gif" border="0" />';

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Brochure Compliance
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br.jpg';
$_COMPLIANCE['logo_width'] = 20; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(1, 2);

// Page Footer Compliance
$_COMPLIANCE['footer'] = '<a rel="nofollow" href="' . Settings::getInstance()->URLS['URL_IDX'] . 'copyright.html">Digital Millennium Copyright Act</a>';

// MRED Copyright Information
$_COMPLIANCE['pages']['copyright'] = array();
$_COMPLIANCE['pages']['copyright']['page_title'] = "DIGITAL MILLENNIUM COPYRIGHT ACT (DMCA) NOTICE";
$_COMPLIANCE['pages']['copyright']['category_html'] = "<h1>DIGITAL MILLENNIUM COPYRIGHT ACT (DMCA) NOTICE</h1>";
$_COMPLIANCE['pages']['copyright']['category_html'] .= "<p>Midwest   Real   Estate   Data   (MRED)   complies   with   the   provisions   of   the   Digital Millennium  Copyright  Act  (DMCA).  If  you  have  a  concern  regarding  the  use  of copyrighted  material  on  anyweb  site containing  MRED listing  content,  please  contact the agent designated to respond to reports alleging copyright infringement.</p>";
$_COMPLIANCE['pages']['copyright']['category_html'] .= "<p>Designated Agent:</p>";
$_COMPLIANCE['pages']['copyright']['category_html'] .= "<p>The  designated  agent  for  MRED  to  receive  notification  of  claimed  infringement  under Title II of the DMCA is:</p>";
$_COMPLIANCE['pages']['copyright']['category_html'] .= "<p>Sarah Burke<br />";
$_COMPLIANCE['pages']['copyright']['category_html'] .= "Rules and Regulations Manager<br />";
$_COMPLIANCE['pages']['copyright']['category_html'] .= "sarah.burke@mredllc.com<br />";
$_COMPLIANCE['pages']['copyright']['category_html'] .= "Midwest Real Estate Data LLC (MRED)<br />";
$_COMPLIANCE['pages']['copyright']['category_html'] .= "2443 Warrenville Road, Suite 600<br />";
$_COMPLIANCE['pages']['copyright']['category_html'] .= "Lisle, Illinois 60532<br />";
$_COMPLIANCE['pages']['copyright']['category_html'] .= "630-955-2744</p>";
$_COMPLIANCE['pages']['copyright']['category_html'] .= "<p>The  DMCA  specifies  that  all  infringement  claims  must  be  in  writing  (either  electronic mail or paper letter) and must include the following:</p>";
$_COMPLIANCE['pages']['copyright']['category_html'] .= "<ol>";
$_COMPLIANCE['pages']['copyright']['category_html'] .= "<li> A physical or electronic signature of the copyright holder or a person authorized to act on his or her behalf;</li>";
$_COMPLIANCE['pages']['copyright']['category_html'] .= "<li> A  description  of  the  copyrighted  work  claimed  to  have  been  infringed  and  multiple copyrighted  works at  a  single  online  site  are  covered  by  a  single  notification,  a representative list of such works at that site;</li>";
$_COMPLIANCE['pages']['copyright']['category_html'] .= "<li> A  description  of  the  material  that  is  claimed  to  be  infringing  or  to  be  the  subject  of infringing activity, and information reasonably sufficient to permit the service provider to locate the material;</li>";
$_COMPLIANCE['pages']['copyright']['category_html'] .= "<li> Information  reasonably  sufficient  to  permit  the  service  provider  to  contact  you,  such as an address, telephone number, and, if available, an electronic mail address;</li>";
$_COMPLIANCE['pages']['copyright']['category_html'] .= "<li> A statement that you have a good faith belief that use of the material in the manner complained of is not authorized by the copyright owner, its agent, or the law; and</li>";
$_COMPLIANCE['pages']['copyright']['category_html'] .= "<li> A statement that the information in the notification is accurate, and under penalty of perjury, that you are authorized to act on behalf of the owner of an exclusive right that is allegedly infringed.</li>";
$_COMPLIANCE['pages']['copyright']['category_html'] .= "</ol>";

// ListTrac tracking
$container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
$_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
