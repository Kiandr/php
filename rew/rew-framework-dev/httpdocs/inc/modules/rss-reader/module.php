<?php

// Title
$title = isset($this->config['title']) ? $this->config['title'] : 'Latest From Our Blog...';

// Limit (Default: 3)
$limit = !empty($this->config['limit']) ? $this->config['limit'] : 3;

// Truncate (Default: 135)
$truncate = !empty($this->config['truncate']) && is_int($this->config['truncate']) ? $this->config['truncate'] : 135;

// Entry target
$target = !empty($this->config['target']) ? $this->config['target'] : '';

// Feed
$feed = !empty($this->config['feed']) ? $this->config['feed'] : Http_Host::getDomainUrl() . 'blog/rss/';

// Skip Loading if Necessary
if (empty(Settings::getInstance()->MODULES['REW_BLOG_INSTALLED']) && strpos($feed, $_SERVER['HTTP_HOST']) !== false) {
    return;
}

$index = $feed;

$items = Cache::getCache($index);

if (empty($items)) {
    if ($feed == Http_Host::getDomainUrl() . 'blog/rss/') {
        include 'blog/common.inc.php';

        // SQL LIMIT
        $query_limit = !empty($_GET['limit']) && is_numeric($_GET['limit']) ? " LIMIT " . intval($_GET['limit']) : " LIMIT 10";

        // SQL ORDER
        $query_order = " ORDER BY `timestamp_published` DESC";

        // Search Blog Entries
        $entries = $db->prepare("SELECT * FROM `" . TABLE_BLOG_ENTRIES . "` WHERE `published` = 'true' AND `timestamp_published` < NOW()" . $query_where . $query_order . $query_limit . ";");
        $entries->execute();

        // Link to Self
        $self = Settings::getInstance()->SETTINGS['URL_RAW'] . $_SERVER['REQUEST_URI'];

        // Channel Link
        $link = substr($self, 0, strpos($self, '/rss') + 1);

        while ($entry = $entries->fetch()) {
            // Format Entry Title
            $item['title'] = Format::stripTags($entry['title']);

            // Strip Snippets
            $item['description'] = preg_replace('!(#([a-zA-Z0-9_-]+)#)!', '', $entry['body']);

            // Set Date
            $item['pubDate'] = $entry['timestamp_published'];

            // Truncate
            if (!empty($truncate)) {
                $item['description'] = Format::truncate($item['description'], $truncate);
            }

            $item['link'] = '/blog/' . $entry['link'] . ".html";
            $items[] = $item;
        }
    } else {
        // Load XML
        $xml = @simplexml_load_string(Util_Curl::executeRequest($feed));

        // Entry items collection
        $items = array();
        if (!empty($xml)) {
            $i = 0;
            foreach ($xml->{"channel"}->{"item"} as $item) {
                $item = (array) $item;

                // Limit reached?
                if ($i >= $limit) {
                    break;
                }

                // Strip HTML
                $item['description'] = strip_tags($item['description']);

                // Truncate
                if (!empty($truncate)) {
                    $item['description'] = Format::truncate(strip_tags($item['description']), $truncate);
                }

                // Add to collection
                $items[] = $item;

                // Increment
                $i++;
            }
        }
    }

    // Save Cache
    Cache::setCache($index, $items);
}
