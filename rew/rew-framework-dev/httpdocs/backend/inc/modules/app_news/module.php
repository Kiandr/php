<?php

/* News items */
$news = array();
$news_cache = new Cache(array('name' => 'tmp/rew-news.json','expires' => (60 * 60 * 4))); // Cache for 4 Hours
if ($news_cache->checkCache()) {
    $news_data = $news_cache->get();
} else {
    $news_data = @file_get_contents('http://www.realestatewebmasters.com/rew-news/');
    $news_cache->save($news_cache->getName(), $news_data);
}

if (!empty($news_data)) {
    $news = json_decode($news_data, true);
}
