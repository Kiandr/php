<?php if (!empty($author)) { ?>

    <h1>
        All Blog Entries by <?= Format::htmlspecialchars($author['name']); ?>
        <a class="uk-float-right uk-h4" href="<?= Format::htmlspecialchars($author['link']); ?>rss/" target="_blank" title="<?= Format::htmlspecialchars($author['first_name']); ?>'s RSS Feed"><i class="uk-icon-rss"></i></a>
    </h1>

    <?php if (!empty($count_entries)) { ?>

        <?php if (!empty($author['blog_profile'])) { ?>
            <p><?=$author['blog_profile']; ?></p>
        <?php } ?>

        <div class="uk-alert">
            <p>Found <strong><?= Format::number($count_entries); ?></strong> <?= Format::plural($count_entries, 'blog entries', 'blog entry'); ?> published by <?= Format::htmlspecialchars($author['name']); ?>.</p>
        </div>

        <?php include $page->locateTemplate('blog', 'misc', 'results'); ?>
        <?php if (!empty($pagination_tpl)) include $pagination_tpl; ?>

    <?php } else { ?>

        <div class="uk-alert">
            <p>There are currently no published blog entries by this author.</p>
        </div>

    <?php } ?>

<?php } else { ?>

    <h1>Blog Error</h1>

    <div class="uk-alert uk-alert-danger">
        <p>The selected blog author could not be found.</p>
    </div>

<?php } ?>
