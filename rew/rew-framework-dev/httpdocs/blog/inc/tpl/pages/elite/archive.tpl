<?php if (!empty($date)) { ?>

    <h1>
        <?= date('F Y', $date); ?>
        <a class="uk-float-right uk-h4" href="<?= Format::htmlspecialchars(sprintf(URL_BLOG_ARCHIVE, date('Y-m', $date))); ?>rss/" target="_blank" title="RSS Feed"><i class="uk-icon-rss"></i></a>
    </h1>

    <?php if (!empty($count_entries)) { ?>

        <div class="uk-alert">
            <p>Found <strong><?= Format::number($count_entries); ?></strong> <?=Format::plural($count_entries, 'blog entries', 'blog entry'); ?> for <strong><?= date('F Y', $date); ?></strong>.</p>
        </div>

        <?php include $page->locateTemplate('blog', 'misc', 'results'); ?>
        <?php if (!empty($pagination_tpl)) include $pagination_tpl; ?>

    <?php } else { ?>

        <div class="uk-alert">
            <p>There are no blog entries for <?=date('F Y', $date); ?>.</p>
        </div>

    <?php } ?>

<?php } else { ?>

    <h1>Blog Error</h1>

    <div class="uk-alert uk-alert-danger">
        <p>The selected archive could not be found.<p>
    </div>

<?php } ?>
