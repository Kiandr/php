<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 50.449390;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -104.616569;
Settings::getInstance()->SETTINGS['map_state'] = 'SK';

// Canadian feed
Settings::getInstance()->LANG = 'en-CA';