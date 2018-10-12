<?php
// Require Pages
if (empty($sitemap)) return;

// Sort Pages
ksort($sitemap, SORT_LOCALE_STRING);
?>

<?php // Show Alpha Bar ?>
<nav class="uk-navbar uk-margin-bottom">
    <ul class="uk-navbar-nav">
        <?php if (isset($sitemap['#'])) { ?>
            <li><a href="#num">#</a></li>
        <?php } ?>
        <?php foreach (range('A', 'Z')  as $letter) { ?>
            <?php if (isset($sitemap[$letter])) { ?>
                <li><a href="#<?= Format::htmlspecialchars($letter); ?>"><?= Format::htmlspecialchars($letter); ?></a>
            <?php } ?>
        <?php } ?>
    </ul>
</nav>

<?php // Show Pages ?>
<?php foreach ($sitemap as $letter => $pages) { ?>

    <?php // Sort Pages ?>
    <?php ksort($pages, SORT_LOCALE_STRING); ?>

    <article class="uk-article">
        <header>
            <h2><a name="<?= ($letter == '#' ? 'num' : Format::htmlspecialchars($letter)); ?>"><?= Format::htmlspecialchars($letter); ?></a></h2>
        </header>
        <ul class="uk-list">
            <?php foreach ($pages as $page) { ?>
                <li><a href="<?= Format::htmlspecialchars($page['link']); ?>"><?= Format::htmlspecialchars($page['title']); ?></a></li>
            <?php } ?>
        </ul>
    </article>

<?php } ?>