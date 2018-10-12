<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 40.6460622;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -111.4979729;
Settings::getInstance()->SETTINGS['map_state'] = 'UT';

// Feed Map Bounds (Don't Generate Geo Points outside of this Polygon)
Settings::getInstance()->SETTINGS['bounds'] = '42.08191667830631 -114.071044921875,42.06560675405716 -110.797119140625,41.145569731009495 -110.928955078125,41.261291493919884 -108.753662109375,36.75649032950515 -108.797607421875,36.77409249464195 -114.312744140625,42.08191667830631 -114.071044921875';
