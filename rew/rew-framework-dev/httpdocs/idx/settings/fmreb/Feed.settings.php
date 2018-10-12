<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 56.72673843;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -111.38111114;
Settings::getInstance()->SETTINGS['map_state'] = 'AB';

Settings::getInstance()->MODULES['REW_IDX_ONBOARD'] = false;

// Canadian feed
Settings::getInstance()->LANG = 'en-CA';