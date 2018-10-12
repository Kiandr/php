<?php if (!empty($category)) { ?>
    <h1 class="small">
    	<?=Format::htmlspecialchars($category['title']); ?>
    	<a href="<?=sprintf(URL_BLOG_CATEGORY, $category['link']); ?>rss/" target="_blank" title="RSS Feed"> <svg class="icon icon--xs"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/inc/skins/ce/img/assets.svg#icon--rss"></use></svg></a>
    </h1>
    <?php if (!empty($category['description'])) { ?>
        <p><?=$category['description']; ?></p>
    <?php } ?>
    <?php if (!empty($count_entries)) { ?>
        <div class="notice">
            <div class="notice__message">
                Found <strong><?=Format::number($count_entries); ?></strong>
                <?=Format::plural($count_entries, 'blog entries', 'blog entry'); ?> about <?=htmlspecialchars($category['title']); ?>.
            </div>
        </div>
        <?php include $page->locateTemplate('blog', 'misc', 'results'); ?>
        <?php if (!empty($pagination_tpl)) include $pagination_tpl; ?>
    <?php } else { ?>
        <div class="notice">
            <div class="notice__message">
                There are currently no blog entries in this category.
            </div>
        </div>
    <?php } ?>
<?php } else { ?>
    <h1>Blog Error</h1>
    <div class="notice notice--negative">
        <div class="notice__message">
            The selected category could not be found.
        </div>
    </div>
<?php } ?>
