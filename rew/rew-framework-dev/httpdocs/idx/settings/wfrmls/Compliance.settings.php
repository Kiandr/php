<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Search Result Limit
$_COMPLIANCE['limit'] = 200;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

    $_COMPLIANCE['disclaimer'][] = '<div>';
    $_COMPLIANCE['disclaimer'][] = '<p><a href="' . Settings::getInstance()->URLS['URL_IDX'] . 'terms-of-use.html">Terms Of Use</a></p>';
    $_COMPLIANCE['disclaimer'][] = '<p>Information deemed reliable but not guaranteed accurate. Buyer to verify all information.</p>';
    $_COMPLIANCE['disclaimer'][] = '<p>The multiple listing information is provided by Wasatch Front Regional Multiple Listing Service, Inc. from a copyrighted compilation of listings. The compilation of listings and each individual listing are &copy; '.date('Y').' Wasatch Front Regional Multiple Listing Service, Inc., All Rights Reserved.</p>';
    $_COMPLIANCE['disclaimer'][] = '<p>The information provided is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.</p>';
    $_COMPLIANCE['disclaimer'][] = '</div><br />';

}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

$_COMPLIANCE['strip_words']['meta_description'] = array('MLS');

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}

// Content Pages
$_COMPLIANCE['pages'] = array();
$_COMPLIANCE['pages']['terms-of-use'] = array();
$_COMPLIANCE['pages']['terms-of-use']['page_title']     = 'Website Terms of Use and Linking Agreement';
$_COMPLIANCE['pages']['terms-of-use']['category_html']  = <<< HTML
<h1>Website Terms of Use and Linking Agreement</h1>
<p>Welcome to our Web site. We offer public access to the most comprehensive and up to date listings of real estate for sale in the State of Utah. These listings are made available by the Wasatch Front Regional Multiple Listing Service, its owner REALTOR&reg; Associations, and the subscribing members of the Wasatch Front Regional MLS. This Web site is made available subject to the terms and conditions of these Website Terms of Use and Linking Agreement ("Terms of Use"). These Terms of Use are between you, the user of this Web site, and Wasatch Front Regional Multiple Listing Service, Inc. ("WFR"). Carefully read these Terms of Use before accessing or using the our website owned and provided by WFR ("WFR's Website").</p>
<p>By accessing or using WFR's Website, you agree to be legally bound by these Terms of Use.  If you do not agree to these Terms of Use, AS THESE TERMS OF USE MAY BE MODIFIED FROM TIME TO TIME, YOU MAY NOT USE WFR's Website, AND YOU MAY NOT create or maintain a link to WFR's Website as set forth in these Terms of Use.</p>
<p>WFR's Website is a free service. Any mobile access may be subject to your carrier's normal rates and fees.</p>
<ol>
    <li>
        <strong>Linking Terms.</strong>
        <ol>
            <li>Upon your acceptance of these Terms of Use, you are authorized to establish a hypertext link (the "Link") from your website ("Your Website") to the home page of WFR's Website. Deep linking to content appearing in the web pages below the home page of WFR's Website is not permitted.</li>
            <li>If you create a Link, Your Website and you shall not do any of the following:
                <ol>
                    <li>Create frames around WFR's Website or otherwise alter the visual presentation of WFR's Website.</li>
                    <li>Expressly state or otherwise imply that WFR is endorsing you, your products or services, or the content of Your Website.</li>
                    <li>Expressly state or otherwise imply an affiliation between you and WFR without the prior written consent of WFR.</li>
                    <li>Misrepresent your relationship with WFR or present false or misleading impressions about WFR's products or services.</li>
                    <li>Disparage WFR's Website or display the Link in a manner that diminishes WFR's goodwill.</li>
                    <li>Include or display any material which is immoral, unethical, illegal or inappropriate for a professional Website.</li>
                    <li>Remove, modify or obscure any copyright or other proprietary right notices and usage restrictions on WFR's Website, or on any content or other materials, or on any copies or versions thereof. All rights not expressly granted are reserved.</li>
                </ol>
            </li>
            <li>You acknowledge and agree that you are not a publisher, distributor, agent, partner, franchiser or endorser of WFR's Website, and WFR is not a publisher, distributor, agent, franchiser or endorser of Your Website. WFR retains exclusive editorial control over WFR's Website and has the right to make administrative or operational decisions it deems necessary or desirable in the normal course of business.</li>
            <li>You warrant to WFR that (1) you have duly registered the domain name of Your Website with all applicable authorities and/or have a license to use Your Website, and you possess all rights necessary to use such the domain name for Your Website, and (2) the content and materials which have been placed within Your Website, or any hyperlink therefrom, do not and will not infringe upon or violate (i) any U.S. copyright, patent, trademark or other proprietary right of a third party, (ii) any applicable law, regulation or non-proprietary right of a third party.</li>
            <li>You agree and acknowledge that you, and not WFR, are responsible for your own Website, and all content and uses of your Website. In addition, you are solely responsible for your own security in linking to or using the WFR Website, and for selecting or implementing any precautions you deem appropriate for your intended use of the WFR Website.</li>
            <li>You acknowledge that by linking to WFR's Website, WFR does not in any way endorse, accept, or approve your Website or any content on your Website. WFR shall not be liable in any way for any claim arising from the operation of your Website or the use of the link to WFR's Website.</li>
        </ol>
    </li>
    <li><strong>Changes to Terms of Use.</strong> WFR reserves the right, in its sole discretion, to modify, update, or otherwise revise these Terms of Use at any time. Such revisions shall be effective immediately upon posting on WFR's Website. By using WFR's Website or creating or maintaining a Link after WFR has posted any modifications, updates or revisions, you agree to be bound by such revised Terms of Use.  In addition to these Terms of Use, additional terms may govern use of certain web pages within WFR's Website or the creation, maintenance and use of any Link; as such terms may be contained on the web pages of WFR's Website. Provided that such additional terms are not contrary to these Terms of Use, by accessing and using such web pages, and creating, using, and maintaining the Link, you agree to be bound by such terms.</li>
    <li><strong>Links to Other Websites.</strong> WFR's Website may contain links to other websites. The linked websites are provided for your convenience and information only and, as such, you access them at your own risk. You agree and acknowledge that WFR is not responsible for the content or anything that may be delivered to you or your computer as a result of accessing any of the linked websites. Specifically, WFR is not responsible and shall have no liability for any viruses or other illicit code that may be downloaded through a link found on WFR's Website, or from WFR's Website.  The content of any linked websites is not under WFR's control, and WFR does not endorse such content, whether or not WFR is affiliated with the owners of such linked websites.</li>
    <li><strong>Limitations on Use.</strong>
        <ol>
            <li>Except with the express prior written consent of WFR, and except for subscribers to WFR's multiple listing services, you may access, download and print materials on WFR's Website only for personal and non-commercial purposes.  Except as expressly provided under these Terms of Use, you may not modify, copy, distribute, transmit, display, perform, reproduce, publish, upload, post, license, frame in another website, use on any other website, create derivate works of, transfer, sell, and/or exploit for commercial use, any information, software, real estate listings, databases or other lists, products or services provided through or obtained from WFR's Website, including by email or other electronic means, without the prior written consent of WFR.  You may create Links from WFR's Website only in accordance with these Terms of Use and.</li>
            <li>You agree not to use WFR's Website or any content or information you may access from WFR's Website to engage in any unauthorized or inappropriate activities or for any unlawful purpose. This limitation includes any conduct that is unlawful, untruthful, misleading, tortious or that is harmful to WFR or any other person or entity.</li>
            <li>You agree not to violate any other person's privacy or other rights. You agree not to bully, intimidate, or harass any user of the WFR Website. You agree not to post or upload any content that is hateful, threatening, pornographic, or inappropriate in the sole judgment of WFR.</li>
            <li>You agree not to use any extraction tool, or any manual, computerized, or automated mechanism to access, compile, download, or extract any content or information from WFR's Website, except as specifically authorized in writing by WFR. This prohibition includes use of any data mining tools, spiders, robots, scrapers or similar tools or technologies.</li>
            <li>WFR prohibits the use of, and you agree not to use, WFR's Website or any information or content on WFR's Website to harvest or collect information about WFR'S subscribers, or to transmit, distribute, or deliver any unsolicited bulk commercial email ("SPAM"). You may not send SPAM to any WFR member or subscriber, property owner, or other person, and you may not authorize others to do so.</li>
        </ol>
    </li>
    <li><strong>Your Representations and Warranties.</strong> You represent and warrant to WFR that any information you provide on WFR's Website will be true, accurate and complete, and will not violate any law, statute, ordinance or regulation.  You warrant that you will not falsely identify yourself or impersonate or falsely represent your affiliation with any person or entity.  Except with the written permission of WFR, you agree that you will not access or attempt to access password protected, secure or non-public areas of WFR's Website.  Unauthorized individuals attempting to access prohibited areas of WFR's Website may be subject to legal prosecution. You further warrant to WFR that you will not use WFR's Website for any purpose that is unlawful or prohibited by these Terms of Use, including without limitation the posting or transmitting of any threatening, libelous, defamatory, obscene, scandalous, inflammatory, pornographic, or profane material.
    </li>
    <li><strong>Use of Mortgage Calculation Tools</strong> The mortgage calculation tools available on WFR's Website are for demonstration purposes only, and are not an offer to lend.  Interest rates shown may differ from actual market interest rates.  WFR does not guarantee the accuracy, sufficiency, correctness, veracity, completeness or timeliness of such service.  You are responsible for confirming the sufficiency and reliability of any such service.</li>
    <li><strong>Proprietary Rights.</strong> You acknowledge and agree that the trademarks of WFR (the "Marks"), WFR's Website, and the content and look and feel of WFR's Website are proprietary, original works of authorship of WFR, or licensed to WFR, protected under United States copyright, trademark, and trade secret laws of general applicability.  You further acknowledge and agree that all right, title and interest in and to the Marks, WFR's Website, and the content and look and feel of WFR's Website are and shall remain with WFR.  You agree not to contest or infringe these rights, directly or indirectly, at any time.  Without the prior written consent of WFR, your modification of the content, use of the content on any other website or networked computer environment, or use of the content for any purpose other than personal, non-commercial use, violates the copyrights, trademarks or other intellectual property rights of WFR or its licensors, and is prohibited.  Except as expressly provided under this Agreement, you may not use on any website, including Your Website, or other materials the Marks or any other trademarks or copyrighted materials appearing on WFR's Website, including without limitation any logos, without the express prior written consent of the owner of the mark or copyright.</li>
    <li><strong>Termination or Interruptions in Service.</strong>
        <ol>
            <li>WFR's Website and access to the content of WFR's Website may from time-to-time be unavailable to you or users of Your Website, whether because of technical failures or interruptions, intentional downtime for service or changes to WFR's Website, or otherwise.  You agree that WFR shall have no liability of any nature to you or any third party for any modifications to WFR's Website, and any interruption or unavailability of access to WFR's Website.</li>
            <li>WFR reserves the right, at any time and for any reason, to change, terminate, limit, suspend, or discontinue the WFR Website, or limit or refuse your access to the WFR Website for any lawful reason. You agree that WFR shall not be liable for doing so.</li>
        </ol>
    </li>
    <li><strong>No Warranties; Exclusion of Liability.</strong> YOU UNDERSTAND AND EXPRESSLY AGREE TO THE FOLLOWING:
        <ol>
            <li>YOUR USE OF AND RELIANCE UPON ANY AND ALL INFORMATION AND SERVICES, INCLUDING WITH RESPECT TO ANY REAL ESTATE LISTING, CONTAINED IN OR PROVIDED THROUGH WFR'S WEBSITE IS AT YOUR SOLE RISK.  SUCH INFORMATION AND SERVICES ARE PROVIDED ON AN "AS IS" AND "AS AVAILABLE" BASIS.  WFR MAKES NO EXPRESS OR IMPLIED REPRESENTATIONS, WARRANTIES, OR GUARANTEES WITH RESPECT TO THE APPROPRIATENESS, ACCURACY, SUFFICIENCY, CORRECTNESS, VERACITY, VALUE, COMPLETENESS, AVAILABILITY, OR TIMELINESS OF THE DATA, METHODS, OR INFORMATION CONTAINED IN OR PROVIDED THROUGH WFR'S WEBSITE.  WFR DOES NOT WARRANT THE FUNCTIONS CONTAINED IN WFR'S WEBSITE WILL BE UNINTERUPTED OR ERROR-FREE, THAT DEFECTS WILL BE CORRECTED, OR THAT THE SERVER THAT MAKES THE CONTENT AVAILABLE WILL BE FREE OF VIRUSES OR OTHER HARMFUL COMPONENTS.  WFR EXPRESSLY DISCLAIMS ALL WARRANTIES OF ANY KIND, WHETHER EXPRESS OR IMPLIED, INCLUDING, BUT NOT LIMITED TO THE IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT.</li>
            <li>IN NO EVENT WILL WFR BE LIABLE TO YOU, ANY USER OF WFR'S WEBSITE OR YOUR WEBSITE, OR ANY THIRD PARTY FOR ANY DECISION MADE OR ACTION TAKEN IN RELIANCE UPON THE INFORMATION CONTAINED IN OR PROVIDED THROUGH WFR'S WEBSITE. ANY MATERIAL YOU DOWNLOAD OR OTHERWISE OBTAIN THROUGH THE USE OF WFR'S WEBSITE IS AT YOUR OWN RISK, AND YOU WILL BE SOLELY RESPONSIBLE FOR ANY DAMAGE TO YOUR EQUIPMENT, SOFTWARE, YOUR WEBSITE, LOSS OF DATA, OR OTHER PERSONAL LOSS THAT RESULTS FROM THE DOWNLOAD OF ANY SUCH MATERIAL.</li>
            <li>TO THE FULLEST EXTENT PERMITTED BY LAW, IN NO EVENT SHALL WFR BE LIABLE TO YOU, OR ANY USER OF WFR'S WEBSITE OR YOUR WEBSITE, OR TO ANY THIRD PARTY FOR ANY LOSS, EXPENSE, OR DAMAGE, OF ANY NATURE, INCLUDING CONSEQUENTIAL, INCIDENTAL, SPECIAL OR PUNITIVE DAMAGES, AND INCLUDING LOST PROFITS OR LOST REVENUE, CAUSED DIRECTLY OR INDIRECTLY BY THE USE OR RELIANCE UPON INFORMATION OR SERVICES OBTAINED BY OR PROVIDED THROUGH WFR'S WEBSITE, OR FOR ANY ERROR OR OMISSION, OR OTHERWISE IN ANY WAY CONNECTED WITH USE OF THIS SITE, WHETHER BASED ON CONTRACT, TORT, INCLUDING NEGLIGENCE, OR OTHERWISE, EVEN IF WFR HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.</li>
        </ol>
    </li>
    <li><strong>Indemnification.</strong>
        You agree to indemnify, defend and hold harmless WFR, its officers,
        directors, employees, shareholders, agents, affiliates, suppliers,
        successors and assigns from and against any and all liability, loss,
        claim, demand, suit, proceeding, damage, cost and expense, including
        reasonable attorneys fees and costs, arising out of or resulting from
        (a) any violation by you of these Terms of Use; (b) the content of
        Your Website; or (c) any negligent acts, errors or omissions of you,
        your agents or contractors.
    </li>
    <li><strong>Miscellaneous.</strong>
        <ol>
            <li>These Terms of Use and all other terms and conditions related to the use of WFR's Website shall be governed by and construed in accordance with the laws of the State of Utah, United States of America, without regard to its conflict of law provisions.  By use of WFR's Website or creating a Link, you hereby consent to the exclusive jurisdiction of and venue in the federal and state courts located in Salt Lake County, State of Utah, United States of America, for all disputes arising out of or related to the use of WFR's Website, any information contained on or provided through WFR's Website, and the creation and maintenance of a Link, and you waive all defenses to the exclusive jurisdiction of such courts.</li>
            <li>If any provisions of these Terms of Use shall be unlawful, void or for any reason unenforceable, then such provision shall be deemed severed from these Terms of Use and shall not affect the validity and enforceability of any remaining provisions.
            </li>
            <li>These Terms of Use represent the entire agreement between WFR and you, and supersede any prior agreements or understandings not incorporated herein. In the event of any inconsistency between these Terms of Use and any future posted terms of use, the lasted posted terms of use shall control.</li>
            <li>There are no third party beneficiaries of these Terms and Conditions.</li>
            <li>If you violate any of these Terms of Use, your permission to use WFR's Website and to create or maintain a Link, and your license to the Marks shall immediately terminate without the necessity of any notice to you.  WFR retains the right to deny access to WFR's Website to anyone at its sole discretion for any reason, including for violation of these Terms of Use.  WFR may, at any time, in its sole discretion for any reason terminate only your right to create and maintain the Link, without affecting your right to otherwise access and use WFR's Website in accordance with these Terms and Conditions.  Upon notice of any termination of your right to create and maintain a Link and license to the Marks, and you shall immediately remove any and all Links and Marks from Your Website.</li>
        </ol>
    </li>
</ol>
HTML;
