<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 40.5852602;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -105.0844230;
Settings::getInstance()->SETTINGS['map_state'] = 'CO';

// Feed Map Bounds (Don't Generate Geo Points outside of this Polygon)
// The Polygon Defined Below includes the Entire State of Colorado (w/ Padding)
Settings::getInstance()->SETTINGS['bounds'] = '41.89028297454537 -110.80810546875,36.05383612509933 -110.63232421875,36.16034858118847 -99.55810546875,42.47641860572304 -99.90966796875,41.89028297454537 -110.80810546875';
