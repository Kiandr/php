<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 44.6965463;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -73.4562693;
Settings::getInstance()->SETTINGS['map_state'] = 'NY';
