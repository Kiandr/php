<h1>
    <?= Format::htmlspecialchars($blog_settings['blog_name']); ?>
    <a class="uk-float-right uk-h4" href="<?= Format::htmlspecialchars(URL_BLOG); ?>rss/" target="_blank" title="RSS Feed"><i class="uk-icon-rss"></i></a>
</h1>

<?php if (!empty($count_entries)) { ?>
    <?php include $page->locateTemplate('blog', 'misc', 'results'); ?>
    <?php if (!empty($pagination_tpl)) include $pagination_tpl; ?>
<?php } else { ?>
    <div class="uk-alert">
        <p>There are currently no blog entries.</p>
    </div>
<?php } ?>
