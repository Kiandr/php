<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 40.7607793;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -111.8910474;
Settings::getInstance()->SETTINGS['map_state'] = 'UT';

// This feed does not allow status change history
Settings::getInstance()->MODULES['REW_IDX_HISTORY_STATUS'] = false;

// Override hardcoded MLS wording.
Lang::$lang['MLS'] = 'Listing';
