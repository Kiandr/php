<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 44.0581728;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -121.3153096;
Settings::getInstance()->SETTINGS['map_state'] = 'OR';
