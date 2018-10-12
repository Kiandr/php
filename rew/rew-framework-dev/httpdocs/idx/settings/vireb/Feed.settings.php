<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 49.166337;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -123.936013;
Settings::getInstance()->SETTINGS['map_state'] = 'BC';

// Canadian feed
Settings::getInstance()->LANG = 'en-CA';