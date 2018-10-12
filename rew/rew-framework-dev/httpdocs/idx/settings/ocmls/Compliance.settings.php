<?php
// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

        $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The data relating to'
                . ' real estate on this website comes in part from the Internet Data'
                . ' Exchange (IDX) program of the Ontario Collective&reg;.'
                . ' The information herein is believed to be accurate and'
                . ' timely, but no warranty as such is expressed or implied.</p>' . PHP_EOL . PHP_EOL;

        $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The trademarks ' . Lang::write('MLS')
                . ', Multiple Listing Service&reg; and the associated logos are owned by The'
                . ' Canadian Real Estate Association (CREA) and identity the quality of'
                . ' services provided by real estate professionals who are members of'
                . ' CREA. Used under license.</p>' . PHP_EOL . PHP_EOL;

        $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The information provided'
                . ' herein must only be used by consumers that have a bona fide interest'
                . ' in the purchase, sale, or lease of real estate and may not be used'
                . ' for any commercial purpose or any other purpose.</p>';

}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;
