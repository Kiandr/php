<?php

// hhmls Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 32.2161111;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -80.7527778;
Settings::getInstance()->SETTINGS['map_state'] = 'SC';

Lang::$lang['MLS_NUMBER'] = 'MLS #';
Lang::$lang['MLS'] = 'MLS';

// This feed does not allow price change history
Settings::getInstance()->MODULES['REW_IDX_HISTORY_PRICE'] = false;

// This feed does not allow status change history
Settings::getInstance()->MODULES['REW_IDX_HISTORY_STATUS'] = false;
