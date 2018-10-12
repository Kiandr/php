<?php if (!empty($_GET['tag'])) { ?>
    <?php if (!empty($tag)) { ?>
        <h1 class="small">
            <a href="<?=URL_BLOG; ?>tags/">Tagged</a> : <?=htmlspecialchars($tag['title']); ?>
            <a href="<?=sprintf(URL_BLOG_TAG, $tag['link']); ?>rss/" target="_blank" title="RSS Feed">
                <i class="icon-rss"></i>
            </a>
        </h1>
        <?php if (!empty($count_entries)) { ?>
            <div class="notice">
                <div class="notice__message">
                    Found <strong><?=Format::number($count_entries); ?></strong> <?=Format::plural($count_entries, 'blog entries', 'blog entry'); ?> tagged as "<?=htmlspecialchars($tag['title']); ?>".
                </div>
            </div>
            <?php include $page->locateTemplate('blog', 'misc', 'results'); ?>
            <?php if (!empty($pagination_tpl)) include $pagination_tpl; ?>
        <?php } else { ?>
            <div class="notice">
                <div class="notice__message">
                    There are currently no blog entries tagged as "<?=htmlspecialchars($tag['title']); ?>".
                </div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <h1>Blog Error</h1>
        <div class="notice notice--negative">
            <div class="notice__message">The selected tag could not be found.</div>
        </div>
    <?php } ?>
<?php } else { ?>
    <h1 class="small">Blog Tags</h1>
    <?php if (!empty($tags)) { ?>
        <div class="tags">
            <?php foreach ($tags as $key => $tag) { ?>
                <?php $size = $min_size + (($tag['total'] - $min_qty) * $step); ?>
                <a href="<?=sprintf(URL_BLOG_TAG, $tag['link']); ?>" style="font-size: <?=$size; ?>%;"><?=htmlspecialchars($tag['title']); ?></a>
            <?php } ?>
        </div>
    <?php } ?>
<?php } ?>
