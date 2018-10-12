<?php

// MLS Compliance Settings
global $_COMPLIANCE;
$_COMPLIANCE = array();

// Search Result Limit
$_COMPLIANCE['limit'] = 500;

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// MLS Disclaimer
$_COMPLIANCE['disclaimer'] = array();

// Only Show on Certain PAges
if (in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'local', 'brochure', 'sitemap', '', 'dashboard'))) {

    // MLS Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Disclaimer: Information herein deemed reliable but not guaranteed by CREB&reg;.</p>';

}

// App is only ever not set when on homepage, display disclaimer on homepage
if(!isset($_GET['app'])){
        $_COMPLIANCE['footer'] = '<h6>The trademarks REALTOR&reg;, REALTORS&reg; and the REALTOR&reg; logo are controlled by The Canadian Real Estate Association (CREA) and identify real estate professionals who are members of CREA. Used under license. The trademarks MLS&reg;, Multiple Listing Service® and the associated logos are owned by The Canadian Real Estate Association (CREA) and identify the quality of services provided by real estate professionals who are members of CREA. Used under license.</h6>';
}

// Search Results Compliance
$_COMPLIANCE['results']['show_mls'] = true;
$_COMPLIANCE['results']['show_office'] = true;
$_COMPLIANCE['results']['fav_first'] = true;

// Details Page Compliance
$_COMPLIANCE['details']['show_office'] = ($_GET['load_page'] != 'map') ? true : false;

// Strip Certain Words from Meta Information
$_COMPLIANCE['strip_words']['meta_description'] = array('realtors', 'realtor', 'mls®', 'mls', 'multiple listings service');
