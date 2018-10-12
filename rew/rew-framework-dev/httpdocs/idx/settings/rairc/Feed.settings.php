<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 27.638643;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -80.397274;
Settings::getInstance()->SETTINGS['map_state'] = 'FL';

// the member would have to request approval
Settings::getInstance()->MODULES['REW_IDX_HISTORY_PRICE'] = false;

// the member would have to request approval
Settings::getInstance()->MODULES['REW_IDX_HISTORY_STATUS'] = false;
