<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

    $_COMPLIANCE['disclaimer'][] = '<div class="disclaimer">';
    $_COMPLIANCE['disclaimer'][] = '<p>Data is deemed reliable but is not guaranteed accurate by the Washington County Board of REALTORS&reg;.<br><a href="' . Settings::getInstance()->URLS['URL_IDX'] . 'privacy-policy.html">Privacy Policy</a>  <a href="' . Settings::getInstance()->URLS['URL_IDX'] . 'terms-of-use.html">Terms of Use</a></p>';
    $_COMPLIANCE['disclaimer'][] = '</div>';

}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;

// Content Pages
$_COMPLIANCE['pages'] = array();

// privacy-policy.html
$_COMPLIANCE['pages']['privacy-policy'] = array();
$_COMPLIANCE['pages']['privacy-policy']['category_html']  = '<h1>Privacy Policy</h1>';
$_COMPLIANCE['pages']['privacy-policy']['category_html'] .= '<p>We are the sole owners of all information gathered about our users and their use of our web site, and that information will never be sold, rented, or released in any manner to any individual, group, corporation, or agency.</p>';
$_COMPLIANCE['pages']['privacy-policy']['category_html'] .= '<p>Additionally, we take very precaution to ensure that information about our users is properly safeguarded.</p>';

// terms-of-use.html
$_COMPLIANCE['pages']['terms-of-use'] = array();
$_COMPLIANCE['pages']['terms-of-use']['category_html']  = '<h1>Terms Of Use</h1>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<p>The Registrant acknowledges entering into a lawful consumer-broker relationship with the Participant.</p>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<p>All data obtained from the IDX is intended only for the Registrant\'s personal, non-commercial use.</p>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<p>The Registrant has a bona fide interest in the purchase, sale, or lease of real estate of the type being offered through the IDX.</p>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<p>The Registrant will not copy, redistribute, or retransmit any of the data or information provided.</p>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<p>The Registrant acknowledges the MLS\'s ownership of and the validity of the copyright in the MLS database.</p>';
$_COMPLIANCE['pages']['terms-of-use']['category_html'] .= '<p>The MLS, other MLS Participants or their duly authorized representatives, are authorized to access the IDX for the sole purpose of monitoring compliance with MLS rules.</p>';
