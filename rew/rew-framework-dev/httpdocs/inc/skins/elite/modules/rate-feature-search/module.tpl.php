<div class="feature-quicksearch">

    <?php if ($heading = $page->variable('feature.searchTitle')) { ?> 
        <h2><?= Format::htmlspecialchars($heading); ?></h2>
    <?php } ?>
    <?php $quicksearch->display(); ?>
    <?php if ($page->variable('feature.searchLinkUrl') && $page->variable('feature.searchLinkText')) { ?>
        <a class="buttonstyle colored-bg2" href="<?= $page->variable('feature.searchLinkUrl'); ?>"><?=$page->variable('feature.searchLinkText'); ?></a>
    <?php } ?>

</div>
