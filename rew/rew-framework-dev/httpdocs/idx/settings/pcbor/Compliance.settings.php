<?php

// Global Compliance
global $_COMPLIANCE;

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', '', 'sitemap', 'dashboard'))) {

	// Require Account Data
	$broker_name  = '[INSERT BROKER NAME]';

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The information provided is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. All properties are subject to prior sale or withdrawal. All information provided is deemed reliable but is not guaranteed accurate, and should be independently verified.</p>';

	if ($_GET['load_page'] == 'brochure') {
		$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Park City Board of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/pcbor.jpg" border="0" style="float: left; width: 150px; margin: 5px 5px 5px 0px;" /> The multiple listing information is provided by Park City Board of Realtors&reg; from a copyrighted compilation of listings. The compilation of listings and each individual listing are &copy; ' . date('Y') . ' Park City Board of Realtors&reg;, All Rights Reserved. Access to the multiple listing information through this website is made available by ' . $broker_name . ' as a member of the Park City Board of Realtors&reg; multiple listing service. No other entity, including a brokerage firm or any franchisor, may be listed in place of the specific Listing Broker on the foregoing notice. Terms of Use: ' . Settings::getInstance()->URLS['URL_IDX'] . 'terms-of-use.html</p>';
	} else {
		$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Park City Board of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/pcbor.jpg" border="0" style="float: left; width: 150px; margin: 5px 5px 5px 0px;" /> The multiple listing information is provided by Park City Board of Realtors&reg; from a copyrighted compilation of listings. The compilation of listings and each individual listing are &copy; ' . date('Y') . ' Park City Board of Realtors&reg;, All Rights Reserved. Access to the multiple listing information through this website is made available by ' . $broker_name . ' as a member of the Park City Board of Realtors&reg; multiple listing service. No other entity, including a brokerage firm or any franchisor, may be listed in place of the specific Listing Broker on the foregoing notice. <a href="' . Settings::getInstance()->URLS['URL_IDX'] . 'terms-of-use.html">Terms of Use</a></p>';
	}
}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('details', 'brochure'))) ? true : false;

$_COMPLIANCE['details']['show_below_remarks'] = true;
$_COMPLIANCE['details']['lang']['provider'] = "Courtesy of: ";

// Brochure Compliance
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/pcbor.jpg';        // Path
$_COMPLIANCE['logo_width'] = 27; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 2; // Paragraph key

// TOS Compliance
$_COMPLIANCE['pages']['terms-of-use'] = array();
$_COMPLIANCE['pages']['terms-of-use']['page_title']     = 'Terms of Use';

$_COMPLIANCE['pages']['terms-of-use']['category_html'] = '<style>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     #wtou_all p, #wtou_all li {';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             margin: 10px;';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     }';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     .wtou_bold li {';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             font-weight: bold;';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     }';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     .wtou_first_indent li {';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             list-style-type: decimal;';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     }';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     .wtou_second_indent li {';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             list-style-type: none;';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     }';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     .wtou_third_indent li {';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             list-style-type: none;';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     }';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     .wtou_no_prefix li {';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             list-style-type: none;';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     }';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     #wtou_h1 {';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             text-decoration: underline;';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             text-align: center;';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     }';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     .wtou_underline {';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             text-decoration: underline;';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     }';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '</style>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<div id="wtou_all">';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<h1 id="wtou_h1">Website Terms of Use</h1>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<p class="wtou_bold">THIS IS A BINDING LEGAL CONTRACT.  CAREFULLY READ THESE WEBSITE TERMS OF USE ("TERMS OF USE") BEFORE USING THE WEBSITE HAVING THE DOMAIN NAME OF <a href="' . Settings::getInstance()->SETTINGS['URL'] . '">' . Settings::getInstance()->SETTINGS['URL'] . '</a> (THE "WEBSITE").  BY ACCESSING OR USING THE WEBSITE, YOU AGREE TO BE BOUND BY THESE TERMS OF USE, AND BE LIABLE TO THE OWNER OF THE WEBSITE (THE "WEBSITE OWNER") FOR ANY NONCOMPLIANCE WITH THESE TERMS OF USE.  IF YOU DO NOT AGREE TO THESE TERMS OF USE, YOU MAY NOT USE THE WEBSITE, AND MAY NOT CREATE OR MAINTAIN A LINK TO THE WEBSITE.</p>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<ol class="wtou_first_indent">';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     <li><span class="wtou_underline">License</span>.  Website Owner hereby grants to you a revocable, limited, nonexclusive license for the duration of your current viewing session to access, download and print only individual real estate listings, comprising factual information and creative content displayed on the Website (the "Listing Content"), solely for personal and non-commercial purposes, and no other purposes, and subject to the limitations set forth in these Terms of Use.  This license will terminate immediately upon any noncompliance by you with any of the terms of these Terms of Use, or at any other time upon notice to you.  All rights not expressly granted in these Terms of Use are reserved.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     <li><span class="wtou_underline">Limitations on Use</span>.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     <ol class="wtou_second_indent">';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             <li>2.1 Except as expressly provided under these Terms of Use or upon Website Owner\'s express prior written consent, you may not modify, copy, distribute, transmit, display, perform, reproduce, publish, upload, post, license, frame in another website, use on any other website, create derivate works of, transfer, sell, and/or exploit for commercial use, any content, software, real estate listings, databases or other lists, products or services provided through or obtained from the Website, including by email or other electronic means, without the prior written consent of Website Owner.  In addition, you may not circumvent any technological measures or features of the Website that are intended to or effectively control access to the Listing Content, or any other protected content or information included on the Website. The Website may contain robot exclusion headers.  The real estate listings displayed on the Website, including the Listing Content, are updated on a real-time basis, and are proprietary or licensed to Website Owner.  You agree that you will not use any robot, spider, scraper or other automated means to access the Website for any purpose without Website Owner\'s express prior written consent.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             <li>2.2 You further agree that you will not:</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             <ol class="wtou_third_indent">';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '                     <li>(a) take any action that imposes or may impose (in Website Owner\'s sole discretion) an unreasonable or disproportionately large load on Website Owner\'s infrastructure;</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '                     <li>(b) copy, reproduce, modify, create derivative works from, distribute, or publicly display any content from the Website without Website Owner\'s express prior written consent and the appropriate third party, as applicable;</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '                     <li>(c) interfere or attempt to interfere with the proper working of the Website or any activities conducted on the Website; or</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '                     <li>(d) bypass Website Owner\'s  robot exclusion headers or other measures Website Owner may use to prevent or restrict access to the Website.';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             </ol>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     </ol>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     <li><span class="wtou_underline">Your Representations and Warranties</span>.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     <ol class="wtou_no_prefix">';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             <li>You represent and warrant to Website Owner that any information you provide on the Website will be true, accurate and complete, and will not violate any law, statute, ordinance or regulation. You warrant that you will not falsely identify yourself or impersonate or falsely represent your affiliation with any person or entity. Except with the written permission of Website Owner, you agree that you will not access or attempt to access password protected, secure or non-public areas of the Website. Unauthorized individuals attempting to access prohibited areas of the Website may be subject to legal prosecution. You further warrant to Website Owner that you will not use the Website for any purpose that is unlawful or prohibited by these Terms of Use, including without limitation the posting or transmitting of any threatening, libelous, defamatory, obscene, scandalous, inflammatory, pornographic, or profane material.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     </ol>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     <li><span class="wtou_underline">Changes to These Terms of Use</span>.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     <ol class="wtou_no_prefix">';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             <li>Website Owner reserves the right, in its sole discretion, to modify, update, or otherwise revise these Terms of Use at any time. Such revisions shall be effective immediately upon posting on the Website. By using the Website or creating, maintaining, or using, if authorized by the Website Owner, a link to the Website, after Website Owner has posted any modifications, updates or revisions, you agree to be bound by such revised Terms of Use. In addition to these Terms of Use, additional terms may govern use of certain web pages within the Website or the creation, maintenance and use of a link to the Website, as such terms may be contained on the web pages of the Website. Provided that such additional terms are not contrary to these Terms of Use, by accessing and using such web pages, and creating, using, and maintaining a link to the Website), you agree to be bound by such terms.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     </ol>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     <li><span class="wtou_underline">Links to Other Websites</span>.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     <ol class="wtou_no_prefix">';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             <li>The Website may contain links to other websites ("Linked Websites"). The Linked Websites are provided for your convenience and information only and, as such, you access them at your own risk. You agree and acknowledge that Website Owner is not responsible for, and does not endorse, the content of or anything that may be delivered to you or your computer as a result of accessing any Linked Websites, whether or not Website Owner is affiliated with the owners of such Linked Websites. Without limiting the generality of the foregoing, Website Owner is not responsible and shall have no liability for any viruses or other illicit code that may be downloaded through a link found on the Website, or by accessing a Linked Website.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     </ol>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     <li><span class="wtou_underline">Terms for Creation of a Link</span>.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     <ol class="wtou_second_indent">';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             <li>6.1 Upon your acceptance of the terms and conditions of these Terms of Use, you are authorized to establish a hypertext link (the "Link") from your website ("Your Website") to the home page of the Website.  Deep linking to content appearing in the web pages below the home page of the Website is not permitted.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             <li>6.2 If you create a Link, Your Website shall not:</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             <ol class="wtou_third_indent">';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '                     <li>(a) Create frames around the Website or otherwise alter the visual presentation of the Website.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '                     <li>(b) Expressly state or otherwise imply that Website Owner is endorsing you, your products or services, or the content of Your Website.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '                     <li>(c) Expressly state or otherwise imply an affiliation between you and Website Owner without the prior written consent of Website Owner.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '                     <li>(d) Misrepresent your relationship with Website Owner or present false or misleading impressions about Website Owner\'s products or services.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '                     <li>(e) Disparage the Website or display the Link in a manner that that diminishes Website Owner\'s goodwill.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '                     <li>(f) Include or display any material which is immoral, unethical, illegal or inappropriate for a professional website.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             </ol>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             <li>6.3 You acknowledge and agree that you are not a publisher, distributor, agent, partner, franchiser or endorser of the Website, and Website Owner is not a publisher, distributor, agent, franchiser or endorser of Your Website. Website Owner retains exclusive editorial control over the Website and has the right to make administrative or operational decisions it deems necessary or desirable in the normal course of business.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             <li>6.4 You warrant to Website Owner that (1) you have duly registered the domain name of Your Website with all applicable authorities and/or have a license to use Your Website, and you possess all rights necessary to use such the domain name for Your Website, and (2) the content of and materials placed on or within Your Website, and any hyperlinks on Your Website, do not and will not (i) infringe upon or violate any U.S. copyright, patent, trademark or other proprietary right of a third party, or (ii) violate any applicable law, statute, regulation, or non-proprietary right of a third party.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     </ol>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     <li><span class="wtou_underline">Proprietary Rights</span>.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     <ol class="wtou_no_prefix">';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             <li>You acknowledge and agree that the trademarks of Website Owner (the "Marks"), the Website, the Listing Content, the compilation or real estate listings, and the content and look and feel of the Website, to the extent protectable, are proprietary, original works of authorship of Website Owner, or licensors of Website Owner, protected under United States and worldwide copyright, trademark, and trade secret laws of general applicability. You further acknowledge and agree that all right, title and interest in and to the Marks, the Website, and the content and look and feel of the Website are and shall remain with Website Owner or its licensors. You agree not to contest or infringe these rights, directly or indirectly, at any time. Without the prior written consent of Website Owner, your modification of the content, use of the content on any other website or networked computer environment, or use of the content for any purpose other than personal, noncommercial use, violates the copyrights, trademarks or other intellectual property rights of Website Owner or its licensors, and is prohibited. Except as expressly provided under this Agreement, you may not use on any website, including Your Website, or on any other materials, the Marks, or any other trademarks or copyrighted materials appearing on the Website, including without limitation any logos, without the express prior written consent of the owner of the mark or copyright.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     </ol>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     <li><span class="wtou_underline">Interruptions in Service</span>.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     <ol class="wtou_no_prefix">';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             <li>The Website and access to the content of the Website may from time-to-time be unavailable to you or users of Your Website, whether because of technical failures or interruptions, intentional downtime for service or changes to the Website, or otherwise. You agree that Website Owner shall have no liability of any nature to you or any third party for any modifications to the Website, and any interruption or unavailability of access to the Website or its content.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     </ol>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     <li><span class="wtou_underline">No Warranties; Exclusion of Liability</span>.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     <ol class="wtou_no_prefix">';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             <li>YOU UNDERSTAND AND EXPRESSLY AGREE TO THE FOLLOWING:</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             <ol class="wtou_third_indent">';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '                     <li>(a) YOUR USE OF AND RELIANCE UPON ANY AND ALL CONTENT AND SERVICES, INCLUDING WITH RESPECT TO ANY REAL ESTATE LISTING, CONTAINED IN OR PROVIDED THROUGH THE WEBSITE IS AT YOUR SOLE RISK. SUCH CONTENT AND SERVICES ARE PROVIDED ON AN "AS IS" AND "AS AVAILABLE" BASIS. WEBSITE OWNER MAKES NO EXPRESS OR IMPLIED REPRESENTATIONS, WARRANTIES, OR GUARANTEES WITH RESPECT TO THE APPROPRIATENESS, ACCURACY, SUFFICIENCY, CORRECTNESS, VERACITY, VALUE, COMPLETENESS, AVAILABILITY, OR TIMELINESS OF THE DATA, METHODS, OR CONTENT CONTAINED IN OR PROVIDED THROUGH THE WEBSITE. WEBSITE OWNER DOES NOT WARRANT THE FUNCTIONS CONTAINED IN THE WEBSITE WILL BE UNINTERRUPTED OR ERROR-FREE, THAT DEFECTS WILL BE CORRECTED, OR THAT THE SERVER THAT MAKES THE CONTENT AVAILABLE WILL BE FREE OF VIRUSES OR OTHER HARMFUL COMPONENTS. WEBSITE OWNER EXPRESSLY DISCLAIMS ALL WARRANTIES OF ANY KIND, WHETHER EXPRESS OR IMPLIED, INCLUDING, BUT NOT LIMITED TO THE IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '                     <li>(b) IN NO EVENT WILL WEBSITE OWNER BE LIABLE TO YOU, ANY USER OF THE WEBSITE OR YOUR WEBSITE, OR ANY THIRD PARTY FOR ANY DECISION MADE OR ACTION TAKEN IN RELIANCE UPON THE CONTENT CONTAINED IN OR PROVIDED THROUGH THE WEBSITE. ANY CONTENT YOU DOWNLOAD OR OTHERWISE OBTAIN THROUGH THE USE OF THE WEBSITE, OR ANY LINKED WEBSITE, IS AT YOUR OWN RISK, AND YOU WILL BE SOLELY RESPONSIBLE FOR ANY DAMAGE TO YOUR EQUIPMENT, SOFTWARE, YOUR WEBSITE, LOSS OF DATA, THEFT, DESTRUCTION, UNAUTHORIZED ACCESS TO OR ALTERATION OF PERSONAL RECORDS, THE RELIANCE UPON OR USE OF DATA, CONTENT, OPINIONS OR OTHER MATERIALS APPEARING ON THE WEBSITE OR A LINKED WEBSITE, OR OTHER PERSONAL LOSS THAT RESULTS FROM THE DOWNLOAD OR USE OF ANY MATERIAL ON THE WEBSITE OR A LINKED WEBSITE.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '                     <li>(c) TO THE FULLEST EXTENT PERMITTED BY LAW, IN NO EVENT SHALL WEBSITE OWNER BE LIABLE TO YOU, OR ANY USER OF THE WEBSITE OR YOUR WEBSITE, OR TO ANY THIRD PARTY FOR ANY LOSS, EXPENSE, OR DAMAGE, OF ANY NATURE, INCLUDING CONSEQUENTIAL, INCIDENTAL, SPECIAL OR PUNITIVE DAMAGES, AND INCLUDING LOST PROFITS OR LOST REVENUE, CAUSED DIRECTLY OR INDIRECTLY BY THE USE OR RELIANCE UPON CONTENT OR SERVICES OBTAINED BY OR PROVIDED THROUGH THE WEBSITE, OR FOR ANY ERROR OR OMISSION, OR OTHERWISE IN ANY WAY CONNECTED WITH USE OF THE WEBSITE, WHETHER BASED ON CONTRACT, TORT, INCLUDING NEGLIGENCE, OR OTHERWISE, EVEN IF WEBSITE OWNER HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             </ol>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     </ol>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     <li><span class="wtou_underline">Indemnification</span>.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     <ol class="wtou_no_prefix">';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             <li>You agree to indemnify, defend and hold harmless Website Owner, its officers, directors, employees, shareholders, agents, affiliates, suppliers, successors and assigns from and against any and all liability, loss, claim, demand, suit, proceeding, damage, cost and expense, including reasonable attorneys fees and costs, arising out of or resulting from (a) any violation by you of these Terms of Use; (b) the content of Your Website; or (c) any negligent acts, errors or omissions of you or your agents or contractors.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     </ol>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     <li><span class="wtou_underline">Miscellaneous</span>.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     <ol class="wtou_second_indent">';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             <li>11.1 These Terms of Use and all other terms and conditions related to the use of the Website shall be governed by and construed in accordance with the laws of the state of the principal place of business or primary residence of the Website Owner, United States of America, without regard to its conflict of law provisions. By use of the Website or creating a Link, 1Gyou hereby consent to the exclusive jurisdiction of and venue in the federal and state courts located in the county and state of the principal place of business or primary residence of the Website Owner, United States of America, for all disputes arising out of or related to the use of the Website, any information contained on or provided through the Website, and the creation and maintenance of a Link, and you waive all defenses to the exclusive jurisdiction of such courts.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             <li>11.2 If any provisions of these Terms of Use shall be unlawful, void or for any reason unenforceable, then such provision shall be deemed severed from these Terms of Use and shall not affect the validity and enforceability of any remaining provisions.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             <li>11.3 These Terms of Use supersede any prior agreements or understandings between you and the Website Owner not incorporated into these Terms of Use. In the event of any inconsistency between these Terms of Use and any future posted Terms of Use, the last posted Terms of Use shall control.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             <li>11.4 There are no third party beneficiaries of these Terms and Conditions.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '             <li>11.5 If you violate any of these Terms of Use, your permission to use the Website and to create or maintain a Link, and your license to the Marks shall immediately terminate without the necessity of any notice to you. Website Owner retains the right to deny access to the Website to anyone at its sole discretion, for any reason, including but not limited to violation of these Terms of Use.  Website Owner may, at any time, in its sole discretion for any reason terminate only your right to create and maintain a Link, without affecting your right to otherwise access and use the Website in accordance with these Terms and Conditions.  Upon notice of any termination of your right to create and maintain a Link and license to the Marks, and you shall immediately remove any and all Links and Marks from Your Website.</li>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '     </ol>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '</ol>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '</div>';
