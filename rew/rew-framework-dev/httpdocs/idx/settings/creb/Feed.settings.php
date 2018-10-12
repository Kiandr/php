<?php

// Feed Settings
if (empty(Settings::getInstance()->SETTINGS['map_latitude']))  Settings::getInstance()->SETTINGS['map_latitude']  = 51.0551490;
if (empty(Settings::getInstance()->SETTINGS['map_longitude'])) Settings::getInstance()->SETTINGS['map_longitude'] = -114.0624380;
Settings::getInstance()->SETTINGS['map_state'] = 'Alberta';

// Feed Map Bounds (Don't Generate Geo Points outside of this Polygon)
// The Polygon Defined Below includes the Entire Province of Alberta (w/ Padding)
Settings::getInstance()->SETTINGS['bounds'] = '60.318969843037294 -120.60791015625,53.13710497148058 -120.52001953125,48.447664946183735 -114.32373046875,48.59320133487014 -109.31396484375,60.14442170721913 -109.18212890625,60.318969843037294 -120.60791015625';

// This feed does not allow price change history
Settings::getInstance()->MODULES['REW_IDX_HISTORY_PRICE'] = false;

// This feed does not allow status change history
Settings::getInstance()->MODULES['REW_IDX_HISTORY_STATUS'] = false;

// Canadian feed
Settings::getInstance()->LANG = 'en-CA';