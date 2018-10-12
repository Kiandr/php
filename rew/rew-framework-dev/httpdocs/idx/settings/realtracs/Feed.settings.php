<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 36.1658899;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -86.7844432;
Settings::getInstance()->SETTINGS['map_state'] = 'TN';

// This feed does not allow price change history unless VOW
Settings::getInstance()->MODULES['REW_IDX_HISTORY_PRICE'] = false;

// This feed does not allow status change history
Settings::getInstance()->MODULES['REW_IDX_HISTORY_STATUS'] = false;
