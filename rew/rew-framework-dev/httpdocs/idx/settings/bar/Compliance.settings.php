<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Search Results Per Page - Limit
$_REQUEST['page_limit'] = 9;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form','dashboard'))) {
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright ' . date('Y') . ', Billings Association of REALTORS&reg; Incorporated and Billings Multiple Listing Service Incorporated. All rights reserved. Information deemed reliable, but not verified or guaranteed. Users are responsible for checking the accuracy, completeness, currency, and status of all information. <a href="' . Settings::getInstance()->URLS['URL_IDX'] . 'terms-and-conditions.html">Terms and Conditions</a></p>';
}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('search_map','local','map'));

$_COMPLIANCE['details']['lang']['provider'] = 'Subject Property listed with:';
$_COMPLIANCE['results']['lang']['provider'] = 'Subject Property listed with:';

// Terms and Conditions Page
$_COMPLIANCE['pages']['terms-and-conditions'] = array();
$_COMPLIANCE['pages']['terms-and-conditions']['page_title']     = 'Terms and Conditions';
$_COMPLIANCE['pages']['terms-and-conditions']['category_html'] = <<<_END
<style type="text/css">
#toc {
	list-style-type: none !important;
}
#toc li {
position:relative;
		 margin-bottom:10px;
}
#toc li span {
display:block;
width:20px;
height:20px;
position:absolute;
left:-30px;
top:2px;
}
</style>
<h1>Terms and Conditions</h1>
<ol id="toc">
<li><span>(i)</span>Copyright <?=date('Y');?> Billings Association of REALTORS&reg; Incorporated and Billings Multiple Listing Service Incorporated. All rights reserved. The sharing of MLS database, or any portion thereof, with any unauthorized third party is strictly prohibited.</li>
<li><span>(ii)</span>Information contained on this site is believed to be reliable, yet, users of this web site are responsible for checking the accuracy, completeness, currency, or suitability of all information. Neither the Billings Association of REALTORS&reg; Incorporated nor the Billings Multiple Listing Service Incorporated. makes any representation, guarantees, or warranties as to the accuracy, completeness, currency, or suitability of the information provided. They specifically disclaim any and all liability for all claims or damages that may result from providing information to be used on the web site, or the information which it contains, including any web sites maintained by third parties, which may be linked to this web site.</li>
<li><span>(iii)</span>The information being provided is for the consumer's personal, noncommercial use, and may not be used for any purpose other than to identify prospective properties which consumers may be interested in purchasing. The user of this site is granted permission to copy a reasonable, and limited number of copies to be used in satisfying the purposes identified in the preceding sentence.</li>
<li><span>(iv)</span>By using this site, you signify your agreement with and acceptance of these terms and conditions. If you do not accept this policy, please do not use this site in any way. Your continued use of this site, and/or its affiliates' sites, following the posting of changes to these terms will mean you accept those changes, regardless of whether you are provided with additional notice of such changes.</li>
</ol>
_END;

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}
