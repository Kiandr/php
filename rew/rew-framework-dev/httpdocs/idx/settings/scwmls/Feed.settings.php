<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 43.0730517;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -89.4012302;
Settings::getInstance()->SETTINGS['map_state'] = 'WI';

Lang::$lang['MLS'] = 'Listings';
