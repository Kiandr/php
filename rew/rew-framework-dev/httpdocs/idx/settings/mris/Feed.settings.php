<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 39.2903848;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -76.6121893;
Settings::getInstance()->SETTINGS['map_state'] = 'DC'; //DC,DE,MD,BC,NJ,OT,PA,SC,TB,VA,WV
