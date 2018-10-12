<?php

// Display featured community list
if (!empty($communities) && is_array($communities)) {
    echo sprintf('<div class="articleset cols community-results" id="%s">', $this->getUID());
    foreach ($communities as $community) {
        echo sprintf('<a class="col stk w1/4 w1/1-sm w1/2-md" href="%s">', $community['url']);
        echo '<div>';
        echo '<div class="img wFill h3/4 fade img--cover">';
        echo sprintf('<img data-src="%s" alt="">', $community['image']);
        echo '</div>';
        echo '</div>';
        echo '<div>';
        echo '<div class="MC txtC pad" style="margin-top: auto; margin-bottom: auto">';
        echo sprintf('<h2>%s</h2>', Format::htmlspecialchars($community['title']));
        echo '</div>';
        echo '</div>';
        echo '</a>';
    }
    echo '</div>';
}