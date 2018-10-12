<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 39.7391536;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -104.9847034;
Settings::getInstance()->SETTINGS['map_state'] = 'CO';

// Compliance
Lang::$lang['IDX_DETAILS_ONBOARD_PAGE_TITLE'] = 'Get Local Neighborhood Information';

// This feed does not allow price change history
Settings::getInstance()->MODULES['REW_IDX_HISTORY_PRICE'] = false;

// This feed does not allow status change history
Settings::getInstance()->MODULES['REW_IDX_HISTORY_STATUS'] = false;

// This feed does not allow website visitors to share listings via social media
Settings::getInstance()->MODULES['REW_IDX_SOCIAL_NETWORK'] = false;