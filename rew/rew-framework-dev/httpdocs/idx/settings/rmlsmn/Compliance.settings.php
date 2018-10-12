<?php

// Global Compliance
global $_COMPLIANCE;

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

// Require Broker Name
$broker_name = '[INSERT BROKER NAME]';
$vendor_name = 'Real Estate Webmasters';

if (in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'local', 'brochure', 'sitemap', '', 'dashboard'))) {

    // Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Regional Multiple Listing Service of Minnesota Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br.jpg" border="0" style="float: left; margin: 10px 10px 10px 0px;" /> The data relating to real estate for sale on this site comes in part from the Broker Reciprocity program of the Regional Multiple Listing Service of Minnesota, Inc. Real Estate listings held by brokerage firms other than ' . $broker_name . ' are marked with the Broker Reciprocity logo or the Broker Reciprocity house icon and detailed information about them includes the names of the listing brokers. ' . $broker_name . ' is not a Multiple Listing Service MLS, nor does it offer MLS access. This website is a service of ' . $broker_name . ', a broker Participant of the Regional Multiple Listing Service of Minnesota, Inc. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Information deemed reliable but not guaranteed. </p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright ' . date("Y") . ' Regional Multiple Listing Service of Minnesota, Inc. All rights reserved. </p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">By searching, you agree to the <a href="' . Settings::getInstance()->URLS['URL_IDX'] . 'eula.html">EULA Terms Agreement</a>.</p>';

}

$_COMPLIANCE['pages']['eula'] = array();
$_COMPLIANCE['pages']['eula']['page_title']     = 'Regional Multiple Listing Service of Minnesota, Inc. EULA Terms Agreement';

$_COMPLIANCE['pages']['eula']['category_html']  = '<h2>';
$_COMPLIANCE['pages']['eula']['category_html']  .= 'Regional Multiple Listing Service of Minnesota, Inc.<br /><br />';
$_COMPLIANCE['pages']['eula']['category_html']  .= 'End-User License Agreement for consumers accessing public MLS and broker web sites, including Broker Reciprocity sites';
$_COMPLIANCE['pages']['eula']['category_html']  .= '</h2>';

$_COMPLIANCE['pages']['eula']['category_html']  .= '<h3>Introduction</h3>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '<p>To satisfy the requirements of RMLS policy (1) This agreement must be available to consumers before they are able to see the IDX data and must be available for review at the beginning of each visit by the consumer.  (2) The consumers must assent affirmatively to the terms by clicking a button that prominently says I AGREE.  (3) Failure of a consumer to assent to the terms must result in no further access to the data for that consumer.</p>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '<p>This can be done unobtrusively by putting a link above the \'Search\' button on the search page.  The link could say "I have read and agree to the terms of the license agreement" (with "license agreement" linked to the full text of the EULA).  The language used in the agreement must be exactly the language presented below, unless RMLS approves alterations in writing in advance.</p>';

$_COMPLIANCE['pages']['eula']['category_html']  .= '<h3>Required Agreement text</h3>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '<p><strong>The following terms and conditions govern all access to and use of this site.  You accept, without limitation or alteration, all the terms and conditions contained herein.  THIS AGREEMENT IS A BINDING CONTRACT AND INCLUDES TERMS THAT LIMIT YOUR LEGAL RIGHTS AND LICENSORS\' LIABILITY TO YOU.  CONSULT YOUR ATTORNEY BEFORE AGREEING IF YOU DO NOT UNDERSTAND ANY OF THE TERMS HERE.</strong></p>';

$_COMPLIANCE['pages']['eula']['category_html']  .= '<p>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '<strong style="display:block; text-align:center;">End-User License Agreement</strong>';
$_COMPLIANCE['pages']['eula']['category_html']  .= 'This End-User License Agreement ("EULA") is a legally binding contract between you; and the owner of this site, ' . $broker_name . ' ("Broker"); and Regional Multiple Listing Service of Minnesota, Inc., d/b/a NorthstarMLS and NorthstarMLS.com ("RMLS"); and the developer of this site, ' . $vendor_name . ' ("Developer").  (Collectively, Broker, RMLS, and Developer are the "Licensors.").';
$_COMPLIANCE['pages']['eula']['category_html']  .= '</p>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '<p>You seek access to real estate listings that are made up of factual information and creative content. This "Licensed Content" appears on this "Licensed Site."  Licensors wish to grant you access to the Licensed Site, but use of this information is limited by the terms of this license.</p>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '<p>In consideration of the mutual covenants contained herein, you and Licensors hereby agree as follows:</p>';

$_COMPLIANCE['pages']['eula']['category_html']  .= '<ol>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '  <li><strong>Access permitted.</strong>  Licensors agree to provide you online access via the World-Wide-Web to the Licensed Content and the Licensed Site for the duration of the current viewing session.  You acknowledge that you will be required to execute a new EULA upon your next visit to the Licensed Site.  You agree not to attempt to access the Licensed Site after the termination of this EULA.<br /><br /></li>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '  <li><strong>Acknowledgement of Title.</strong>  You acknowledge that all right, title, and interest in the copyrights and other intellectual property rights in the Licensed Site and the Licensed Content reside at all times in Licensors and their licensors.<br /><br />';
$_COMPLIANCE['pages']['eula']['category_html']  .= '  The trademarks, logos, and service marks (collectively the "Marks" or "Mark") appearing on the Licensed Site are registered and unregistered marks of Licensors and others.  Neither this EULA nor the Licensed Site grants you any right to use any Mark displayed on the Licensed Site or any other Marks of Licensors.<br /><br /></li>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '  <li><strong>License.</strong>  Licensors hereby grant you a revocable, limited, nonexclusive license during the term of this EULA to duplicate, distribute and display the Licensed Content and the Licensed Site, solely for your personal, non-commercial use, and subject to the limitations set forth in this EULA.  Licensors grant nonexclusive licenses and not exclusive licenses or assignments.  All rights not expressly granted in this EULA are reserved.<br /><br /></li>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '  <li><strong>Limited Use.</strong>  You will not:';
$_COMPLIANCE['pages']['eula']['category_html']  .= '    <ol style="list-style:lower-alpha">';
$_COMPLIANCE['pages']['eula']['category_html']  .= '      <li>Use the Licensed Site, Licensed Content, or both for any purpose other than a personal, non-commercial one;<br /><br /></li>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '      <li>Disclose any of the Licensed Content, including factual content, to any third party except in furtherance of your personal real estate transaction, and then only to the extent necessary;<br /><br /></li>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '      <li>Gather, or attempt to gather, by any automated means, including "screen scraping" or "database scraping," factual content or any other portion of the Licensed Content from the Licensed Site; or<br /><br /></li>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '      <li>Employ the Licensed Content, the Licensed Site, or both for any unlawful purpose.</li>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '    </ol></li>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '  <li><strong>License revoked.</strong>  Your license to use the Licensed Content and the Licensed Site is immediately revoked, without notice from Licensors, in the event that you breach any provision of this EULA.<br /><br /></li>';

$_COMPLIANCE['pages']['eula']['category_html']  .= '  <li><strong>General terms.</strong>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '    <ol style="list-style:lower-alpha">';
$_COMPLIANCE['pages']['eula']['category_html']  .= '      <li><strong>Term and termination.</strong>  Any party may terminate this EULA upon notice to another.  In the event of termination, all licenses hereunder immediately terminate, and you agree to discontinue accessing and attempting to access the Licensed Site.  The terms of sections 2, 4, and 6 of this EULA shall survive its termination.<br /><br /></li>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '      <li><strong>Disclaimer of warranties.</strong>  LICENSORS PROVIDE THE LICENSED SITE AND LICENSED CONTENT ON AN "AS IS," "AS AVAILABLE" BASIS.  LICENSORS MAKE NO WARRANTY AS TO THE ACCURACY, COMPLETENESS, CURRENCY, OR RELIABILITY OF THE LICENSED CONTENT.  YOU ARE ADVISED THAT FACTUAL MATERIAL IN THE LICENSED CONTENT, THOUGH DEEMED RELIABLE, MAY CONTAIN ERRORS AND IS SUBJECT TO REVISION AT ALL TIMES.  YOU ARE ADVISED TO CONFIRM ALL FACTUAL MATERIAL UPON WHICH YOU INTEND TO RELY IN ANY TRANSACTION.  THE LICENSORS EXPRESSLY DISCLAIM ALL WARRANTIES WITH RESPECT TO THE LICENSED SITE AND THE LICENSED CONTENT, INCLUDING THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE.  Possible errors in the Licensed Content include, but are not limited to, incorrect measurements, improper classification of rooms and features according to local zoning codes, incorrect status with regard to availability for sale, incorrect photograph, and incorrect information about improvements.<br /><br /></li>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '      <li><strong>Limitations and exclusions of liability.</strong>  UNDER NO CIRCUMSTANCES SHALL THE LICENSORS BE LIABLE TO YOU OR ANYONE ELSE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, EXEMPLARY, OR PUNITIVE DAMAGES THAT RESULT FROM THE USE OF, OR INABILITY TO USE, THE LICENSED SITE, THE LICENSED CONTENT, OR BOTH.  YOUR SOLE REMEDY, IN THE EVENT THE LICENSORS OR ANY ONE OF THEM BREACH THIS EULA, SHALL BE TO TERMINATE THIS EULA.  IN THE EVENT THE LIMITATIONS SET FORTH IN THE PRECEDING TWO SENTENCES ARE HELD BY ANY COURT TO BE UNENFORCEABLE, LICENSORS SHALL NOT IN ANY EVENT BE LIABLE TO YOU OR ANYONE ELSE FOR DAMAGES OF ANY KIND IN EXCESS OF $500.<br /><br /></li>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '      <li><strong>Indemnification.</strong>  You will defend, indemnify and hold the Licensors harmless from and against any and all liability, damages, loss or expense (including reasonable fees of attorneys and other professionals) in any claim, demand, action or proceeding initiated by any third-party against the Licensors arising from any of your acts, including without limitation violating this or any other agreement or any law.<br /><br /></li>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '      <li><strong>Assignment.</strong>  You may not assign or delegate this EULA or any obligations, rights, or duties hereunder.  Any attempted or purported assignment or delegation in contravention of this section is null and void.<br /><br /></li>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '      <li><strong>Integration and severability.</strong>  This EULA contains the entire understanding of the parties and supersedes all previous oral and written agreements on the subject matter hereof.  Each provision of this EULA is severable from the whole, and if one provision is declared invalid, the other provisions shall remain in full force and effect.<br /><br /></li>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '      <li><strong>Governing law.</strong>  This EULA shall be governed by, and construed in accordance with, the laws of the State of Minnesota applicable to contacts made and performed in Minnesota, but without regard to the choice of law and conflicts of law provisions thereof.  The parties hereby agree that any dispute under this EULA shall have its forum in the state or federal courts located in Ramsey County, Minnesota, in the United States of America, and the parties hereby consent to personal jurisdiction therein and expressly waive any defenses to personal jurisdiction, including <em>forum non conveniens</em>.</li>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '    </ol></li>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '<h2>Digital Millennium Copyright Act (DMCA) Notice</h2>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '<p>The Digital Millennium Copyright Act of 1998, 17 U.S.C. &sect; 512 (the "DMCA"), provides recourse for copyright owners who believe that material appearing on the Internet infringes their rights under U.S. copyright law. If you believe in good faith that any content or material made available in connection with our website or services infringes your copyright, you (or your agent) may send us a notice requesting that the content or material be removed, or access to it blocked. Notices and counter-notices should be sent in writing by mail to Michael Bisping, Director, Customer Relations, Regional Multiple Listing Service of Minnesota, Inc, 2550 University Avenue West, Suite 259S</p>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '<p>Saint Paul, MN  55114 or by email to mbisping@northstarmls.com.  Questions can be directed by phone to 651-251-3200.</p>';
$_COMPLIANCE['pages']['eula']['category_html']  .= '<p>The DMCA requires that your notice of alleged copyright infringement include the following information: (1) description of the copyrighted work that is the subject of claimed infringement; (2) description of the alleged infringing content and information sufficient to permit us to locate the content; (3) contact information for you, including your address, telephone number and e-mail address; (4) a statement by you that you have a good faith belief that the content in the manner complained of is not authorized by the copyright owner, or its agent, or by the operation of any law; (5) a statement by you, signed under penalty of perjury, that the information in the notification is accurate and that you have the authority to enforce the copyrights that are claimed to be infringed; and (6) a physical or electronic signature of the copyright owner or a person authorized to act on the copyright owner\'s behalf. Failure to include all of the above information may result in the delay of the processing of your complaint.</p>';


// EULA Notice for refine bar
$_COMPLIANCE['eula_notice'] = '<div>By searching, you agree to the <a href="' . Settings::getInstance()->URLS['URL_IDX'] . 'eula.html">EULA Terms Agreement</a></div>';

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Regional Multiple Listing Service of Minnesota Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br-house.gif" border="0" />';

// Search Results, Display Agent Name
$_COMPLIANCE['results']['show_agent'] = false;

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = false;

// Listing Details, Display Office Name
if (!in_array($_GET['load_page'], array('map'))) {
	$_COMPLIANCE['details']['show_office'] = true;
}

// Brochure logo
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br.jpg'; // Path
$_COMPLIANCE['logo_width'] = 17; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
