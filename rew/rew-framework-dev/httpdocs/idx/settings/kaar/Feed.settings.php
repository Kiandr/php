<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 35.960638;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -83.920739;
Settings::getInstance()->SETTINGS['map_state'] = 'TN';

// This feed does not allow price change history
Settings::getInstance()->MODULES['REW_IDX_HISTORY_PRICE'] = false;

// This feed does not allow status change history
Settings::getInstance()->MODULES['REW_IDX_HISTORY_STATUS'] = false;
