<h1 class="small">
	<?=Format::htmlspecialchars($blog_settings['blog_name']); ?>
	<a href="<?=URL_BLOG; ?>rss/" target="_blank" title="RSS Feed"><i class="icon-rss"></i></a>
</h1>

<?php if (!empty($count_entries)) { ?>
    <?php include $page->locateTemplate('blog', 'misc', 'results'); ?>
    <?php if (!empty($pagination_tpl)) include $pagination_tpl; ?>
<?php } else { ?>
    <div class="msg">
        <p>There are currently no blog entries.</p>
    </div>
<?php } ?>