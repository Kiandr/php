<?php if (!empty($author)) { ?>

    <h1 class="small">
    	All Blog Entries by <?=Format::htmlspecialchars($author['name']); ?>
    	<a href="<?=$author['link']; ?>rss/" target="_blank" title="<?=Format::htmlspecialchars($author['first_name']); ?>'s RSS Feed"><i class="icon-rss"></i></a>
    </h1>

    <?php if (!empty($count_entries)) { ?>

        <?php if (!empty($author['blog_profile'])) { ?>
            <p><?=$author['blog_profile']; ?></p>
        <?php } ?>

        <div class="msg">
            <p>Found <strong><?=Format::number($count_entries); ?></strong> <?=Format::plural($count_entries, 'blog entries', 'blog entry'); ?> published by <?=Format::htmlspecialchars($author['name']); ?>.</p>
        </div>

        <?php include $page->locateTemplate('blog', 'misc', 'results'); ?>
        <?php if (!empty($pagination_tpl)) include $pagination_tpl; ?>

    <?php } else { ?>

        <div class="msg">
            <p>There are currently no published blog entries by this author.</p>
        </div>

    <?php } ?>

<?php } else { ?>

    <h1>Blog Error</h1>

    <div class="msg negative">
        <p>The selected blog author could not be found.</p>
    </div>

<?php } ?>