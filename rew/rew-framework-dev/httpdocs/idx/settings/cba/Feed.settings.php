<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 47.6962304;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -122.2347786;
Settings::getInstance()->SETTINGS['map_state'] = 'WA';

Lang::$lang['MLS_NUMBER'] = 'CBA ID';
Lang::$lang['MLS'] = 'CBA ID';
