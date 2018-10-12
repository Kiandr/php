<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 50.674181;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -120.331494;
Settings::getInstance()->SETTINGS['map_state'] = 'BC';

// Canadian feed
Settings::getInstance()->LANG = 'en-CA';