<?php

if (!empty($developments)) {
    echo '<div class="articleset cols locale-results">';
    foreach ($developments as $development) {

?>
<a class="col stk w1/3 w1/1-sm w1/2-md" <?=(!empty($development['url']) ? 'href="' . $development['url'] . '"' : ''); ?>>
    <div>
        <div class="img wFill h4/3 fade img--cover">
            <?php if (!empty($development['image'])) { ?>
                <img data-src="<?=$development['image']; ?>" alt="">
            <?php } elseif (!empty($placeholder)) { ?>
                <img data-src="<?=$placeholder; ?>" alt="">
            <?php } ?>
        </div>
    </div>
    <div>
        <div class="pad">
            <?php if (!empty($development['tags'])) { ?>
                <span class="bdg"><?=implode('</span><span class="bdg">', $development['tags']); ?></span>
            <?php } ?>
        </div>
        <div class="BM txtC pad">
            <h2><?=Format::htmlspecialchars($development['title']); ?></h2>
            <?php if (!empty($development['subtitle'])) { ?>
                <p class="description"><?=Format::htmlspecialchars($development['subtitle']); ?></p>
            <?php } ?>
        </div>
    </div>
</a>
<?

    }
    echo '</div>';
}