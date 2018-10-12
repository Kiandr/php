<?php

// Require Multi-IDX setup
if (empty(Settings::getInstance()->IDX_FEEDS)) {
    return;
}

// HTTP host
$host = $_SERVER['HTTP_HOST'];

if (empty($this->config['heading'])) {
    $this->config['heading'] = Settings::get('search_area_label');
}

// Build links
$feeds = array();
foreach (Settings::getInstance()->IDX_FEEDS as $feed => $settings) {
    // Skip feed
    if (is_array($this->config['feeds']) && !in_array($feed, $this->config['feeds'])) {
        continue;
    }

    // Skip feed agent subdomain control
    $commingled_feeds = array();
    $idx = Util_IDX::getIdx($feed, false, false);
    if ($idx->isCommingled()) {
        $commingled_feeds = $idx->getFeeds();
    }
    if (Settings::getInstance()->SETTINGS['agent'] != 1
        && !in_array($feed, Settings::getInstance()->SETTINGS['agent_idxs'])
        && !in_array($feed, Settings::getInstance()->SETTINGS['team_idxs'])
        && (
            empty($commingled_feeds)
            || (array_intersect($commingled_feeds, Settings::getInstance()->SETTINGS['agent_idxs']) == array()
            && array_intersect($commingled_feeds, Settings::getInstance()->SETTINGS['team_idxs']) == array())
        )) {
        continue;
    }

    // Include query string parameters
    $vars = array('create_search', 'lead_id');
    $query = array();

    // Build query string
    foreach ($vars as $var) {
        if (isset($_GET[$var])) {
            $query[$var] = $_GET[$var];
        }
    }

    // Display mode
    if ($this->config['mode'] === 'inline') {
        // Query string
        $url = Settings::getInstance()->SETTINGS['URL_RAW'] . $_SERVER['REQUEST_URI'];
        $qs = parse_url($url, PHP_URL_QUERY);

        // Parse as array
        $qs_vars = array();
        parse_str($qs, $qs_vars);
        unset($qs_vars['p'], $qs_vars['feed']);


        // Non-default feed
        if (!(Settings::getInstance()->IDX_FEED_DEFAULT === $feed || (empty(Settings::getInstance()->IDX_FEED_DEFAULT) && Settings::getInstance()->IDX_FEED == $feed))) {
            $qs_vars['feed'] = $feed;
        }

        // Link
        $link = !empty($qs_vars) ? '?' . http_build_query($qs_vars) : parse_url($url, PHP_URL_PATH);

        // Add to collection
        $feeds[] = array(
            'name' => $feed,
            'link' => $link,
            'title' => $settings['title'],
            'active' => $feed == Settings::getInstance()->IDX_FEED,
        );
    } else {
        // Path
        $path = '/idx/';
        $app = $_GET['app']?:$this->getContainer()->getPage()->info('app');

        if ($app == 'idx-map') {
            $path = '/idx/map/';
        }

        // Add to collection
        $feeds[] = array(
            'name' => $feed,
            'link' => Settings::getInstance()->SETTINGS['URL_RAW'] . $path
            . ((Settings::getInstance()->IDX_FEED_DEFAULT === $feed || (empty(Settings::getInstance()->IDX_FEED_DEFAULT) && Settings::getInstance()->IDX_FEED == $feed)) ? '' : $feed . '/')
            . (!empty($_GET['page_request']) ? $_GET['page_request'] :'')
            . (!empty($query) ? '?' . http_build_query($query) : ''),
            'title' => $settings['title'],
            'active' => $feed == Settings::getInstance()->IDX_FEED,
        );
    }
}
