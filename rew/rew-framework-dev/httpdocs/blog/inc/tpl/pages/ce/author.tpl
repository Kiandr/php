<?php if (!empty($author)) { ?>
    <h1 class="small">
    	All Blog Entries by <?=Format::htmlspecialchars($author['name']); ?>
    	<a href="<?=$author['link']; ?>rss/" target="_blank" title="<?=Format::htmlspecialchars($author['first_name']); ?>'s RSS Feed"> <svg class="icon icon--xs"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/inc/skins/ce/img/assets.svg#icon--rss"></use></svg></a>
    </h1>
    <?php if (!empty($count_entries)) { ?>
        <?php if (!empty($author['blog_profile'])) { ?>
            <p><?=$author['blog_profile']; ?></p>
        <?php } ?>
        <div class="notice">
            <div class="notice__message">
                Found <strong><?=Format::number($count_entries); ?></strong>
                <?=Format::plural($count_entries, 'blog entries', 'blog entry'); ?> published by <?=htmlspecialchars($author['name']); ?>.
            </div>
        </div>
        <?php include $page->locateTemplate('blog', 'misc', 'results'); ?>
        <?php if (!empty($pagination_tpl)) include $pagination_tpl; ?>
    <?php } else { ?>
        <div class="notice">
            <div class="notice__message">
                There are currently no published blog entries by this author.
            </div>
        </div>
    <?php } ?>
<?php } else { ?>
    <h1>Blog Error</h1>
    <div class="notice notice--negative">
        <div class="notice__message">
            The selected blog author could not be found.
        </div>
    </div>
<?php } ?>
