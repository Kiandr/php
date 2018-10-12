<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('details', 'brochure')) || (!empty(Settings::getInstance()->SETTINGS['registration']) && in_array($_GET['load_page'], array('', 'search', 'search_map', 'map', 'streetview', 'birdseye', 'directions', 'local', 'sitemap', 'search_form', 'dashboard')))) {

    // Require account data
    $broker_name = '[INSERT BROKER HERE]';

    // Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
        . 'The data relating to real estate on this web site comes in part from the'
        . ' Internet Data Exchange program of North Carolina Regional MLS LLC, and'
        . ' is updated as of <?=date(\'F jS, Y \a\t g:ia T\', strtotime($last_updated)); ?>.'
        . ' All information is deemed reliable but not guaranteed and should be independently'
        . ' verified. All properties are subject to prior sale, change, or withdrawal.'
        . ' Neither listing broker(s) nor ' . $broker_name . ' shall be responsible for'
        . ' any typographical errors, misinformation, or misprints, and shall be'
        . ' held totally harmless from any damages arising from reliance upon these'
        . ' data. &copy; 2016 North Carolina Regional MLS LLC</p>';
}

// Search Results, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));
