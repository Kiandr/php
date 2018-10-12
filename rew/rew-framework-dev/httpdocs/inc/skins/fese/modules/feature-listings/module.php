<?php

// DB connection
$db = DB::get('cms');

// Website settings container
$settings = Settings::getInstance();

// Featured
$feature = [
    'listing' => [],
    'development' => []
];

// Featured developments module
$featuredDevelopments = false;
if (!empty(Settings::getInstance()->MODULES['REW_DEVELOPMENTS'])) {
    $featuredDevelopments = new Module_FeaturedDevelopments($db, $settings);
    if ($development = $featuredDevelopments->getResult()) {
        $feature['development'][] = $development;
    }
}

// Featured listings module
if (!empty(Settings::getInstance()->MODULES['REW_FEATURED_LISTINGS'])) {
    $featuredListings = new Module_FeaturedListings($db, $settings);
    $listings = $featuredListings->getResults($development ? 1 : 2);
    if (!empty($listings)) {
        $feature['listing'] = $listings;
    }
}

// No featured listings - use another development
if (empty($feature['listing']) && !empty($featuredDevelopments)) {
    if ($development = $featuredDevelopments->getResult()) {
        $feature['development'][] = $development;
    }
}

// Display featured records
foreach ($feature as $type => $results) {
    foreach ($results as $result) {
        $template = sprintf('%s/templates/%s.tpl.php', __DIR__, $type);
        if (file_exists($template)) {
            require $template;
        }
    }
}
