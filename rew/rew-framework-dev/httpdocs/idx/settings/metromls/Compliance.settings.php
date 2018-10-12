<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">IDX information is provided exclusively for consumers\' personal, non-commercial use and that it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Information is supplied by seller and other third parties and has not been verified.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright ' . date("Y") . ' - Metro MLS - All Rights Reserved</p>';

}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;
$_COMPLIANCE['results']['lang']['provider'] = 'Listed Courtesy of ';

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map'));
$_COMPLIANCE['details']['lang']['provider'] = 'Listed Courtesy of ';

// Page Footer Compliance
$_COMPLIANCE['footer'] = '<a href="' . Settings::getInstance()->URLS['URL_IDX'] . 'copyright.html">DMCA Copyright Notice</a>';

// NWMLS Copyright Information
$_COMPLIANCE['pages']['copyright'] = array();
$_COMPLIANCE['pages']['copyright']['page_title']   = 'DMCA Copyright Notice';
$_COMPLIANCE['pages']['copyright']['category_html']   = '<h1>DMCA Copyright Notice</h1>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '<h2>NOTICE AND PROCEDURE FOR MAKING CLAIMS OF COPYRIGHT INFRINGEMENT</h2>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '<p>We respect the intellectual property of others and have established the following procedure for receiving notice of infringement in compliance with the Digital Millennium Copyright Act (“DMCA”).</p>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '<p>NOTE: THE FOLLOWING INFORMATION IS PROVIDED SOLELY FOR NOTIFYING THE SERVICE PROVIDERS</p>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '<p>REFERENCED BELOW THAT YOUR COPYRIGHTED MATERIAL MAY HAVE BEEN INFRINGED.</p>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '<p>Pursuant to Title 17, United States Code, Section 512(c)(2), all notifications of claimed copyright infringement relative to our system or Site should be sent <strong>ONLY</strong> to our Designated Agent.</p>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '<p>Written notification must be submitted to the following designated Agent via letter or email:</p>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '<p><strong>Service Provider:</strong><br/>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= 'Real Estate Webmasters</p>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '<p><strong>Name of Agent Designated to Receive Notification of Claimed Infringement:</strong><br/>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= 'Attorney Alan Deutch</p>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '<p><strong>Address to Which Notification Should be Sent:</strong><br/>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '7670 N. Port Washington Road, Suite 200, Fox Point, Wisconsin 53217</p>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '<p><strong>Email of Designated Agent:</strong><br/>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= 'copyright@deutch.com</p>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '<p><strong>Telephone Number of Designated Agent:</strong><br/>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '(414) 247- 9958                  </p>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '<p><strong>Facsimile Number of Designated Agent:</strong><br/>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '(414) 247-9959</p>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '<p>Under Title 17, United States Code, Section 512(c)(3)(A), the Notification of Claimed Infringement must  include the following:</p>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '<ol>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '<li>An electronic or physical signature of the owner or the person authorized to act on behalf of the owner of the copyright interest;</li>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '<li>Identification of the copyrighted work (or works) that you claim has been infringed;</li>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '<li>A description of the material that you claim is infringing, and the location, where the original or an authorization copy of the copyrighted work exists (for example, the URL of the page of the website where it is lawfully posted; the name, edition and pages of a book from which an excerpt was copied, etc.)</li>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '<li>A clear description of where the infringing material is located on our website, including as applicable its URL, so that we can locate the material</li>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '<li>Your address, telephone number and e-mail address</li>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '<li>A statement that you have a good-faith belief that the disputed use is not authorized by the copyrighted owner, its agent, or the law; and</li>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '<li>A statement by you, made under penalty of perjury, that the above information in your notice is accurate and that you are the copyright owner or authorized to act on the copyright owner’s behalf.</li>';
$_COMPLIANCE['pages']['copyright']['category_html']  .= '</ol>';
