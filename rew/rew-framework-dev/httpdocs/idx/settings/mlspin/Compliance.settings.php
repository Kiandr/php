<?php

/* IDX Compliance Settings */
$_COMPLIANCE = array();

/* Display Update Time */
$_COMPLIANCE['update_time'] = false;

/* Disclaimer Text */
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

    /* Disclaimer */
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The property listing data and information, or the Images, set forth herein were provided to MLS Property Information Network, Inc. from third party sources, including sellers, lessors and public records, and were compiled by MLS Property Information Network, Inc. The property listing data and information, and the Images, are for the personal, non commercial use of consumers having a good faith interest in purchasing or leasing listed properties of the type displayed to them and may not be used for any purpose other than to identify prospective properties which such consumers may have a good faith interest in purchasing or leasing. MLS Property Information Network, Inc. and its subscribers disclaim any and all representations and warranties as to the accuracy of the property listing data and information, or as to the accuracy of any of the Images, set forth herein. </p>';

}

/* Search Results, Display Agent Name */
$_COMPLIANCE['results']['show_agent'] = true;

/* Search Results, Display Office Name */
$_COMPLIANCE['results']['show_office'] = true;

/* Listing Details, Display Agent Name */
$_COMPLIANCE['details']['show_agent'] = true;

/* Listing Details, Display Office Name */
$_COMPLIANCE['details']['show_office'] = true;

?>