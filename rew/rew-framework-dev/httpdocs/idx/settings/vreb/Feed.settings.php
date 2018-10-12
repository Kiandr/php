<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 48.4286111;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -123.3655556;
Settings::getInstance()->SETTINGS['map_state'] = 'BC';

// Canadian feed
Settings::getInstance()->LANG = 'en-CA';