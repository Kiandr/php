<?php

/**
 * Pagination template
 * @var array $links
 * @var array $prev
 * @var array $next
 */

?>
<?php if (!empty($links)) { ?>
    <div class="nav_pagination">
        <?php if (!empty($prev)) { ?>
            <a href="<?=$prev['url']; ?>" class="prev">&lt;&lt;</a>
        <?php } ?>
        <?php if (!empty($links)) { ?>
            <?php foreach ($links as $link) { ?>
                <a href="<?=$link['url']; ?>"<?=!empty($link['active']) ? ' class="current"' : ''; ?>>
                    <?=$link['link']; ?>
                </a>
            <?php } ?>
        <?php } ?>
        <?php if (!empty($next)) { ?>
            <a href="<?=$next['url']; ?>" class="next">&gt;&gt;</a>
        <?php } ?>
    </div>
<?php } ?>