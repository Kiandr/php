<?php if (!empty($category)) { ?>

    <h1>
        <?= Format::htmlspecialchars($category['title']); ?>
        <a class="uk-float-right uk-h4" href="<?=sprintf(URL_BLOG_CATEGORY, $category['link']); ?>rss/" target="_blank" title="RSS Feed"><i class="uk-icon-rss"></i></a>
    </h1>

    <?php if (!empty($category['description'])) { ?>
        <p><?= $category['description']; ?></p>
    <?php } ?>

    <?php if (!empty($count_entries)) { ?>

        <div class="uk-alert">
            <p>Found <strong><?= Format::number($count_entries); ?></strong> <?= Format::plural($count_entries, 'blog entries', 'blog entry'); ?> about <?= Format::htmlspecialchars($category['title']); ?>.</p>
        </div>

        <?php include $page->locateTemplate('blog', 'misc', 'results'); ?>
        <?php if (!empty($pagination_tpl)) include $pagination_tpl; ?>

    <?php } else { ?>

        <div class="uk-alert">
            <p>There are currently no blog entries in this category.</p>
        </div>

    <?php } ?>

<?php } else { ?>

    <h1>Blog Error</h1>

    <div class="uk-alert uk-alert-danger">
        <p>The selected category could not be found.<p>
    </div>

<?php } ?>
