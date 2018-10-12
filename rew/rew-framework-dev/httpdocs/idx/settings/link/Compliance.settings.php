<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

        $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright ' . date("Y") . ' LINK, All Rights Reserved.</p>';
        $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Data last updated ' . date('m/d/Y', strtotime(\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->lastUpdated())) . '. ';
        $_COMPLIANCE['disclaimer'][] = 'Some properties may no longer be for sale if they have gone under contract, ';
        $_COMPLIANCE['disclaimer'][] = 'have sold, or are no longer being offered for sale since the last update. ';
        $_COMPLIANCE['disclaimer'][] = 'All Information believed to be correct but not guaranteed.</p>';

}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));
