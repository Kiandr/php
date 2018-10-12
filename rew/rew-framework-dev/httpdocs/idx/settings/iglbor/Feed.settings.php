<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 42.5016994;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -96.205992;
Settings::getInstance()->SETTINGS['map_state'] = 'IA';
