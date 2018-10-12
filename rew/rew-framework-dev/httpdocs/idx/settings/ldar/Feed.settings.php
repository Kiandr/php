<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 49.692433;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -112.838361;
Settings::getInstance()->SETTINGS['map_state'] = 'AB';

// Canadian feed
Settings::getInstance()->LANG = 'en-CA';