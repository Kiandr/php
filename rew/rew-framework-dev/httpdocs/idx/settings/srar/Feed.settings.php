<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 52.110428;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -106.657715;
Settings::getInstance()->SETTINGS['map_state'] = 'SK';

// Canadian feed
Settings::getInstance()->LANG = 'en-CA';
