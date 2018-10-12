<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 42.986889;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -81.246208;
Settings::getInstance()->SETTINGS['map_state'] = 'ON';

Lang::$lang['IDX_SEARCH_REFINE_BUTTON'] = 'Refine ' . Lang::write('MLS') . ' Search';
Lang::$lang['IDX_SEARCH_BUTTON'] = 'Refine ' . Lang::write('MLS') . ' Search';
Lang::$lang['IDX_SEARCH_LISTINGS_BUTTON'] = 'Refine ' . Lang::write('MLS') . ' Search';
Lang::$lang['IDX_FIND_BUTTON'] = 'Refine ' . Lang::write('MLS') . ' Search';
Lang::$lang['IDX_START_SEARCH_BUTTON'] = 'Refine ' . Lang::write('MLS') . ' Search';

// Canadian feed
Settings::getInstance()->LANG = 'en-CA';