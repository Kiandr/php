<?php

/**
 * This code is pretty crazy - it relies on the #navigation# snippet
 * and it used to replace dynamic elements with featured modules
 *  - @todo: ability to define module options?
 */

// Logged in as user...
$user = User_Session::get();
if ($user->isValid()) {
    $lead_name = Format::trim($user->info('first_name'). ' ' . $user->info('last_name'));
    $lead_name = !empty($lead_name) ? $lead_name : 'My Profile';
}

// Viewing agent sub-domain, use standard nav snippet
if (Settings::getInstance()->SETTINGS['agent'] !== 1) {
    $snippet_name = 'nav-primary';
    $navigation = rew_snippet($snippet_name, false);
    return;
}

// Snippet name
$snippet_name = 'navigation';

// Navigation snippet
$navigation = rew_snippet($snippet_name, false);
if (empty($navigation)) {
    return;
}

// Create temp container for dynamic module instances
$container = $this->getContainer()->getPage()->container('navigation-feature');

// Load snippet contents from cache
$cacheIndex = Skin_LEC2015::NAVIGATION_CACHE_INDEX;
$cachedCode = Cache::getCache($cacheIndex);
if (!is_null($cachedCode)) {
    $navigation = $cachedCode;
    return;
}

// Parse as HTML to replace dynamic elements
$replace = array();
$dom = new DOMDocument();
@$dom->loadHTML($navigation);
$xpath = new DOMXPath($dom);
foreach (array(
    'data-featured-agents',
    'data-featured-listing',
    'data-featured-community',
    'data-search-agents',
    'data-search-listings',
    'data-search-communities'
) as $feature) {
    if ($nodes = $xpath->query('//div[@' . $feature . ']')) {
        // Process dynamic nodes
        foreach ($nodes as $node) {
            // @todo: Featured module options
            //$options = $node->getAttribute($feature);
            //$options = json_decode($options, true);

            // Load module HTML
            switch ($feature) {
                // Featured agent
                case 'data-featured-agents':
                    $html = $container->addModule('agents', array(
                        'template'      => 'feature.tpl.php',
                        'thumbnails'    => '220x275/f',
                        'mode'          => 'spotlight',
                        'limit'         => 2
                    ))->display(false);
                    break;

                // Featured listing
                case 'data-featured-listing':
                    $html = $container->addModule('idx-listings', array(
                        'template'  => 'feature.tpl.php',
                        'thumbnails'=> '670x500/f',
                        'limit' => 1
                    ))->display(false);
                    break;

                // Featured community
                case 'data-featured-community':
                    $html = $container->addModule('communities', array(
                        'template'  => 'feature.tpl.php',
                        'mode'      => 'featured',
                        'thumbnails'=> '670x500/f',
                        'searchUrl' => true,
                        'hasImage'  => true,
                        'loadTags'  => true,
                        'loadStats' => false,
                        'loadImages'=> 1,
                        'limit'     => 1
                    ))->display(false);
                    break;

                // Search agents form
                case 'data-search-agents':
                    ob_start();
                    require_once $this->locateFile('search/agents.tpl.php');
                    $html = ob_get_clean();
                    break;

                // Search listings form
                case 'data-search-listings':
                    ob_start();
                    require_once $this->locateFile('search/listings.tpl.php');
                    $html = ob_get_clean();
                    break;

                // Search communities form
                case 'data-search-communities':
                    ob_start();
                    require_once $this->locateFile('search/communities.tpl.php');
                    $html = ob_get_clean();
                    break;
            }

            // Replace module placeholder
            $placeholder = $dom->createElement('module');
            $placeholder->nodeValue = '#' . $feature . '#';
            $node->parentNode->replaceChild($placeholder, $node);

            // Add HTML to replacement collection
            $replace['<module>#' . $feature . '#</module>'] = $html;
        }
    }
}

// Remove <!DOCTYPE><html><body>
$dom->removeChild($dom->doctype);
$dom->replaceChild($dom->firstChild->firstChild->firstChild, $dom->firstChild);

// Navigation markup
$navigation = trim($dom->saveHTML());

// Replace dynamic elements
if (!empty($replace)) {
    foreach ($replace as $module => $content) {
        $navigation = str_replace($module, $content, $navigation);
    }
}

// Save parsed snippet to cache
Cache::setCache($cacheIndex, $navigation);
