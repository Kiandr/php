<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude'])) Settings::getInstance()->SETTINGS['map_latitude'] = 20.8837167;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude'] = -156.4509353;
Settings::getInstance()->SETTINGS['map_state'] = 'HI';