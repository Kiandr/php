<?php

// Display feed links
if (!empty($feeds) && is_array($feeds)) {
    if (count($feeds) > 1) {
        echo '<div class="nav nav--tabs -mar-bottom-sm -clear"><ul class="idx-feed-select nav__list">';
        foreach ($feeds as $feed) {
            echo sprintf('<li class="-mar-bottom-xs@xs nav__item%s"><a class="nav__link -pad-sm -text-xs"%s%s>%s</a></li>',
                (!empty($feed['active']) ? ' -is-current' : ''),
                (!empty($this->config['dataAttr']) ? sprintf(' data-feed="%s" ', $feed['name']) : ''),
                sprintf(' href="%s"', $feed['link']),
                Format::htmlspecialchars($feed['title'])
            );
        }
        echo '</ul></div>';
    }
}
