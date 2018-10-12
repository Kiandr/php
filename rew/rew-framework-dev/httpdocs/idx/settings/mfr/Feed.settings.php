<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']   = 27.3364347;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude']  = -82.5306527;
Settings::getInstance()->SETTINGS['map_state'] = 'FL';

// Feed Map Bounds (Don't Generate Geo Points outside of this Polygon)
// The Polygon Defined Below includes the Entire State of Florida (w/ Padding)
Settings::getInstance()->SETTINGS['bounds'] = '31.48988992269542 -88.92333984375,29.444700139807143 -88.92333984375,29.022871657889752 -83.91357421875,24.152100084915894 -82.72705078125,23.77529123645138 -80.6396484375,24.71223804062941 -78.94775390625,26.80969035548689 -75.82763671875,27.474486050371436 -79.25537109375,29.630771207229 -79.617919921875,31.152020804634834 -80.13427734375,31.48988992269542 -88.92333984375';

// This feed does not allow status change history
Settings::getInstance()->MODULES['REW_IDX_HISTORY_STATUS'] = false;
