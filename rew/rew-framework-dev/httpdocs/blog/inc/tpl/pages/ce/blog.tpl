<div class="divider">
	<span class="divider__label -left"><?=Format::htmlspecialchars($blog_settings['blog_name']); ?></span>
	<a href="<?=URL_BLOG; ?>rss/" target="_blank" title="RSS Feed" class="-right -pad-horizontal-xs"> <svg class="icon icon--xs"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/inc/skins/ce/img/assets.svg#icon--rss"></use></svg></a>
</div>

<?php if (!empty($count_entries)) { ?>
    <?php include $page->locateTemplate('blog', 'misc', 'results'); ?>
    <?php if (!empty($pagination_tpl)) include $pagination_tpl; ?>
<?php } else { ?>
    <div class="notice -mar-top">
        <div class="notice__message">
             There are currently no blog entries.
        </div>
    </div>
<?php } ?>
