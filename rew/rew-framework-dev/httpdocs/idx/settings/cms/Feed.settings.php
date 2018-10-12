<?php

// Disable map search for cms listings
if ($_GET['load_page'] === 'search_map') {
    Settings::getInstance()->MODULES['REW_IDX_MAPPING'] = false;
}

// Disable onboard for cms listings
Settings::getInstance()->MODULES['REW_IDX_ONBOARD'] = false;