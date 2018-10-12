<?php if (!empty($_GET['tag'])) { ?>

    <?php if (!empty($tag)) { ?>

        <h1 class="small">
        	<a href="<?=URL_BLOG; ?>tags/">Tagged</a> : <?=Format::htmlspecialchars($tag['title']); ?>
        	<a href="<?=sprintf(URL_BLOG_TAG, $tag['link']); ?>rss/" target="_blank" title="RSS Feed"><i class="icon-rss"></i></a>
        </h1>

        <?php if (!empty($count_entries)) { ?>

            <div class="msg">
                <p>Found <strong><?=Format::number($count_entries); ?></strong> <?=Format::plural($count_entries, 'blog entries', 'blog entry'); ?> tagged as "<?=Format::htmlspecialchars($tag['title']); ?>".</p>
            </div>

            <?php include $page->locateTemplate('blog', 'misc', 'results'); ?>
            <?php if (!empty($pagination_tpl)) include $pagination_tpl; ?>

        <?php } else { ?>

            <div class="msg">
                <p>There are currently no blog entries tagged as "<?=Format::htmlspecialchars($tag['title']); ?>".</p>
            </div>

        <?php } ?>

    <?php } else { ?>

        <h1>Blog Error</h1>

	    <div class="msg negative">
			<p>The selected tag could not be found.<p>
	    </div>

    <?php } ?>

<?php } else { ?>

    <h1 class="small">Blog Tags</h1>

	<?php if (!empty($tags)) { ?>
	    <div class="tags">
            <?php foreach ($tags as $key => $tag) { ?>
                <?php $size = $min_size + (($tag['total'] - $min_qty) * $step); ?>
                <a href="<?=sprintf(URL_BLOG_TAG, $tag['link']); ?>" style="font-size: <?=$size; ?>%;"><?=Format::htmlspecialchars($tag['title']); ?></a>
            <?php } ?>
	    </div>
    <?php } ?>

<?php } ?>