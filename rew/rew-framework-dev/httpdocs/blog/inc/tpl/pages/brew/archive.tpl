<?php if (!empty($date)) { ?>

    <h1 class="small">
    	<?=date('F Y', $date); ?>
    	<a href="<?=sprintf(URL_BLOG_ARCHIVE, date('Y-m', $date)); ?>rss/" target="_blank" title="RSS Feed"><i class="icon-rss"></i></a>
    </h1>

    <?php if (!empty($count_entries)) { ?>

        <div class="msg">
            <p>Found <strong><?=Format::number($count_entries); ?></strong> <?=Format::plural($count_entries, 'blog entries', 'blog entry'); ?> for <strong><?=date('F Y', $date); ?></strong>.</p>
        </div>

        <?php include $page->locateTemplate('blog', 'misc', 'results'); ?>
        <?php if (!empty($pagination_tpl)) include $pagination_tpl; ?>

    <?php } else { ?>

        <div class="msg">
            <p>There are no blog entries for <?=date('F Y', $date); ?>.</p>
        </div>

    <?php } ?>

<?php } else { ?>

    <h1>Blog Error</h1>

    <div class="msg negative">
		<p>The selected archive could not be found.<p>
    </div>

<?php } ?>