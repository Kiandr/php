<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 26.3586885;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -80.0830984;
Settings::getInstance()->SETTINGS['map_state'] = 'FL';

// No Social Networking Allowed
Settings::getInstance()->MODULES['REW_IDX_SOCIAL_NETWORK'] = false;
