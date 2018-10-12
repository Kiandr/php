<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 45.988201;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -66.638603;
Settings::getInstance()->SETTINGS['map_state'] = 'NB';

// Canadian feed
Settings::getInstance()->LANG = 'en-CA';