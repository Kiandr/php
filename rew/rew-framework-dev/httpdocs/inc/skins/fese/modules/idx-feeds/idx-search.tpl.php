<?php

// Display feed links
if (!empty($feeds) && is_array($feeds)) {
    if (count($feeds) > 1) {
        echo '<ul class="idx-feed-select">';
        foreach ($feeds as $feed) {
            echo sprintf('<li><a %s%s>%s</a></li>',
                (!empty($this->config['disabled'])
                    ? sprintf('href="%s"', $feed['link'])
                    : sprintf('data-feed="%s"', $feed['name'])
                ),
                (!empty($feed['active']) ? ' class="selected"' : ''),
                Format::htmlspecialchars($feed['title'])
            );
        }
        echo '</ul>';
    }
}