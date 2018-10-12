<?php if (!empty($category)) { ?>

    <h1 class="small">
    	<?=Format::htmlspecialchars($category['title']); ?>
    	<a href="<?=sprintf(URL_BLOG_CATEGORY, $category['link']); ?>rss/" target="_blank" title="RSS Feed"><i class="icon-rss"></i></a>
    </h1>

    <?php if (!empty($category['description'])) { ?>
        <p><?=$category['description']; ?></p>
    <?php } ?>

    <?php if (!empty($count_entries)) { ?>

        <div class="msg">
            <p>Found <strong><?=Format::number($count_entries); ?></strong> <?=Format::plural($count_entries, 'blog entries', 'blog entry'); ?> about <?=Format::htmlspecialchars($category['title']); ?>.</p>
        </div>

        <?php include $page->locateTemplate('blog', 'misc', 'results'); ?>
        <?php if (!empty($pagination_tpl)) include $pagination_tpl; ?>

    <?php } else { ?>

        <div class="msg">
            <p>There are currently no blog entries in this category.</p>
        </div>

    <?php } ?>

<?php } else { ?>

    <h1>Blog Error</h1>

    <div class="msg negative">
		<p>The selected category could not be found.<p>
    </div>

<?php } ?>