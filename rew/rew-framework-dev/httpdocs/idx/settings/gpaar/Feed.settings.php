<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 55.171690;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -118.801426;
Settings::getInstance()->SETTINGS['map_state'] = 'AB';

// Canadian feed
Settings::getInstance()->LANG = 'en-CA';