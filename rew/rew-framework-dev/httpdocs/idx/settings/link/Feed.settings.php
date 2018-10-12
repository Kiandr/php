<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 41.3852497;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -70.5306618;
Settings::getInstance()->SETTINGS['map_state'] = 'MA';

Lang::$lang['MLS_NUMBER'] = 'LINK ID ';
Lang::$lang['MLS'] = 'LINK ID';
Lang::$lang['MLS_NUMBER_PANEL'] = 'LINK ID';
Lang::$lang['IDX_DETAILS_PAGE_TITLE'] = '{Address}, {AddressCity} Property Listing: LINK ID {ListingMLS}';
Lang::$lang['IDX_DETAILS_MAP_PAGE_TITLE'] = 'Listing Map of {Address}, {AddressCity} {AddressState} {AddressZipCode} (LINK ID {ListingMLS})';
Lang::$lang['IDX_DETAILS_BIRDSEYE_PAGE_TITLE'] = 'Bird\'s Eye View of {Address}, {AddressCity} {AddressState} {AddressZipCode} (LINK ID {ListingMLS})';
Lang::$lang['IDX_DETAILS_STREETVIEW_PAGE_TITLE'] = 'Google Streetview of {Address}, {AddressCity} {AddressState} {AddressZipCode} (LINK ID {ListingMLS})';
Lang::$lang['IDX_DETAILS_ONBOARD_PAGE_TITLE'] = 'Get Local Neighborhood Information for {Address}, {AddressCity} {AddressState} {AddressZipCode} (LINK ID {ListingMLS})';
Lang::$lang['IDX_DETAILS_INQUIRE_PAGE_TITLE'] = 'Find out more about {Address}, {AddressCity} {AddressState} {AddressZipCode} (LINK ID {ListingMLS})';
Lang::$lang['IDX_DETAILS_SHOWING_PAGE_TITLE'] = 'Request a Property Showing of {Address}, {AddressCity} {AddressState} {AddressZipCode} (LINK ID {ListingMLS})';
Lang::$lang['IDX_DETAILS_SHARE_PAGE_TITLE'] = 'Share {Address}, {AddressCity} {AddressState} {AddressZipCode} (LINK ID {ListingMLS}) with a Friend';
Lang::$lang['IDX_DETAILS_PHONE_PAGE_TITLE'] = 'Send {Address} (LINK ID {ListingMLS}) to your mobile device';
Lang::$lang['IDX_DETAILS_SAVE_PAGE_TITLE'] = 'Save Listing: {Address}, {AddressCity} {AddressState} {AddressZipCode} (LINK ID {ListingMLS})';
Lang::$lang['IDX_DETAILS_PAGE_TITLE_MISSING'] = 'Listing Not Found: LINK ID {ListingMLS}';
Lang::$lang['INQUIRE_ASK_ABOUT'] = 'I was searching for a Property and found this listing (LINK ID {ListingMLS}). Please send me more information regarding {Address}, {AddressCity}, {AddressState}, {AddressZipCode}. Thank you!';
Lang::$lang['INQUIRE_REQUEST_SHOWING'] = 'I\'d like to request a showing of {Address}, {AddressCity}, {AddressState}, {AddressZipCode} (LINK ID {ListingMLS}).\nThank you!';
