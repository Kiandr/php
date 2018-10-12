<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 46.786939;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -92.098194;
Settings::getInstance()->SETTINGS['map_state'] = 'MN';

// Defaults
Lang::$lang['IDX_MAIN_PAGE_TITLE']                  = 'Search Listings';
Lang::$lang['IDX_MAIN_META_DESCRIPTION']            = '';
Lang::$lang['IDX_MAIN_META_KEYWORDS']               = '';

// Details Page
Lang::$lang['IDX_DETAILS_PAGE_TITLE']               = '<?=$listing[\'Address\'];, <?=$listing[\'AddressCity\']; Property Listing: MLS&reg; # <?=$listing[\'ListingMLS\'];';
Lang::$lang['IDX_DETAILS_META_DESCRIPTION']         = '<?=substr($listing[\'ListingRemarks\'],0,255);...';
Lang::$lang['IDX_DETAILS_META_KEYWORDS']            = '';

// Details Page - Listing Not Found
Lang::$lang['IDX_DETAILS_PAGE_TITLE_MISSING']       = 'Listing Not Found: MLS&reg; # <?=strip_tags($_REQUEST[\'pid\']);';
Lang::$lang['IDX_DETAILS_META_DESCRIPTION_MISSING'] = '';
Lang::$lang['IDX_DETAILS_META_KEYWORDS_MISSING']    = '';

// Map This Property Page
Lang::$lang['IDX_DETAILS_MAP_PAGE_TITLE']           = 'Listing Map - <?=$listing[\'Address\'];, <?=$listing[\'AddressCity\'];: MLS&reg; # <?=$listing[\'ListingMLS\'];';
Lang::$lang['IDX_DETAILS_MAP_META_DESCRIPTION']     = '<?=substr($listing[\'ListingRemarks\'],0,255);...';
Lang::$lang['IDX_DETAILS_MAP_META_KEYWORDS']        = '';

// Print Page
Lang::$lang['IDX_DETAILS_PRINT_PAGE_TITLE']         = 'Print Page - <?=$listing[\'Address\'];, <?=$listing[\'AddressCity\'];: MLS&reg; # <?=$listing[\'ListingMLS\'];';
Lang::$lang['IDX_DETAILS_PRINT_META_DESCRIPTION']   = '<?=substr($listing[\'ListingRemarks\'],0,255);...';
Lang::$lang['IDX_DETAILS_PRINT_META_KEYWORDS']      = '';

// Register Page
Lang::$lang['IDX_REGISTER_PAGE_TITLE']              = 'Register';
Lang::$lang['IDX_REGISTER_META_DESCRIPTION']        = '';
Lang::$lang['IDX_REGISTER_META_KEYWORDS']           = '';
