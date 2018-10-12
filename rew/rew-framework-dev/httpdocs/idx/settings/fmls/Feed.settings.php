<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 33.7489954;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -84.3879824;
Settings::getInstance()->SETTINGS['map_state'] = 'GA';


// Remove listing information from 'Ask About this Property' and 'Request a showing'
Lang::$lang['INQUIRE_ASK_ABOUT'] = 'I was searching for a Property and found this listing. Please send me more information. Thank you!';
Lang::$lang['INQUIRE_REQUEST_SHOWING'] = "I'd like to request a showing of this listing. Thank you!";

// Listing Walkscore Meta Information (MLS Compliance Disallows Usage of Listing Information)
Lang::$lang['IDX_DETAILS_WALKSCORE_PAGE_TITLE'] = 'WalkScore&reg;';
Lang::$lang['IDX_DETAILS_WALKSCORE_META_DESCRIPTION'] = '';

// Listing Inquire Form Meta Information (MLS Compliance Disallows Usage of Listing Information)
Lang::$lang['IDX_DETAILS_INQUIRE_PAGE_TITLE'] = 'Find out more about this property';
Lang::$lang['IDX_DETAILS_INQUIRE_META_DESCRIPTION'] = '';

// Listing Request Showing Meta Information (MLS Compliance Disallows Usage of Listing Information)
Lang::$lang['IDX_DETAILS_SHOWING_PAGE_TITLE'] = 'Request a Propety Showing';
Lang::$lang['IDX_DETAILS_SHOWING_META_DESCRIPTION'] = '';

// IDX Details Page - Send to Friend
Lang::$lang['IDX_DETAILS_SHARE_PAGE_TITLE'] = 'Share This Property With a Friend';

// Remove Onboard disclaimer
Lang::$lang['IDX_MAP_ONBOARD_DISCLAIMER'] = '';

// Listing Birds Eye Meta Information (MLS Compliance Disallows Usage of Listing Information)
Lang::$lang['IDX_DETAILS_BIRDSEYE_PAGE_TITLE'] = 'Bird\'s Eye View';
Lang::$lang['IDX_DETAILS_BIRDSEYE_META_DESCRIPTION'] = 'Bird\'s Eye View';

// Listing Streetview Meta Information (MLS Compliance Disallows Usage of Listing Information)
Lang::$lang['IDX_DETAILS_STREETVIEW_PAGE_TITLE'] = 'Google Streetview';
Lang::$lang['IDX_DETAILS_STREETVIEW_META_DESCRIPTION'] = 'Google Streetview';

// Listing Onboard Meta Information (MLS Compliance Disallows Usage of Listing Information)
Lang::$lang['IDX_DETAILS_ONBOARD_PAGE_TITLE'] = 'Get Local Neighborhood Information';
Lang::$lang['IDX_DETAILS_ONBOARD_META_DESCRIPTION'] = 'Get Local Neighborhood Information';
