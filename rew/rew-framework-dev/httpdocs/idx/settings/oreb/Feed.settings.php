<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 44.056936;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -79.382467;
Settings::getInstance()->SETTINGS['map_state'] = 'Ontario';

// Canadian feed
Settings::getInstance()->LANG = 'en-CA';