<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 39.74521015328692;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -86.15203857421875;
Settings::getInstance()->SETTINGS['map_state'] = 'IN';

// Feed Map Bounds (Don't Generate Geo Points outside of this Polygon)
// The Polygon Defined Below includes the Entire State of Indiana (w/ Padding)
Settings::getInstance()->SETTINGS['bounds'] = '42.94543270980246 -87.8961181640625,38.312640278457096 -88.2586669921875,37.30580995033667 -87.9840087890625,38.11410229504182 -84.6771240234375,40.26807001503618 -84.1497802734375,44.4309032966905 -84.1497802734375,42.94543270980246 -87.8961181640625';

// Website Lang Override
Lang::$lang['MLS_NUMBER'] = "BLC&reg; #";
Lang::$lang['MLS'] = "BLC&reg;";
Lang::$lang['IDX_DETAILS_PAGE_TITLE_MISSING']            = "Listing Not Found: " . Lang::$lang['MLS_NUMBER'] . "{ListingMLS}";
Lang::$lang['IDX_DETAILS_SHARE_PAGE_TITLE']              = 'Share {Address}, {AddressCity} {AddressState} {AddressZipCode} (' . Lang::$lang['MLS_NUMBER'] . '{ListingMLS}) with a Friend';
Lang::$lang['IDX_DETAILS_SHOWING_PAGE_TITLE']            = 'Request a Property Showing of {Address}, {AddressCity} {AddressState} {AddressZipCode} (' . Lang::$lang['MLS_NUMBER'] . '{ListingMLS})';
Lang::$lang['IDX_DETAILS_INQUIRE_PAGE_TITLE']            = 'Find out more about {Address}, {AddressCity} {AddressState} {AddressZipCode} (' . Lang::$lang['MLS_NUMBER'] . '{ListingMLS})';
Lang::$lang['IDX_DETAILS_ONBOARD_PAGE_TITLE']            = 'Get Local Neighborhood Information for {Address}, {AddressCity} {AddressState} {AddressZipCode} (' . Lang::$lang['MLS_NUMBER'] . '{ListingMLS})';
Lang::$lang['IDX_DETAILS_STREETVIEW_PAGE_TITLE']         = 'Google Streetview of {Address}, {AddressCity} {AddressState} {AddressZipCode} (' . Lang::$lang['MLS_NUMBER'] . '{ListingMLS})';
Lang::$lang['IDX_DETAILS_BIRDSEYE_PAGE_TITLE']           = 'Bird\'s Eye View of {Address}, {AddressCity} {AddressState} {AddressZipCode} (' . Lang::$lang['MLS_NUMBER'] . '{ListingMLS})';
Lang::$lang['IDX_DETAILS_MAP_PAGE_TITLE']                = 'Listing Map of {Address}, {AddressCity} {AddressState} {AddressZipCode} (' . Lang::$lang['MLS_NUMBER'] . '{ListingMLS})';
Lang::$lang['IDX_DETAILS_PAGE_TITLE']                    = '{Address}, {AddressCity} Property Listing: ' . Lang::$lang['MLS_NUMBER'] . '{ListingMLS}';
