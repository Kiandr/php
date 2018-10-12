<?php

// Module Disabled
if (empty(Settings::getInstance()->MODULES['REW_FEATURED_COMMUNITIES'])) return;

// No communities added
if (empty($communities)) return;

?>
<div class="cols">
    <?php foreach ($communities as $i => $community) { ?>
        <?php $url = $community['url'] ?: $community['search_url']; ?>
        <a<?=($url ? sprintf(' href="%s"', $url) : ''); ?> class="col stk w1/3 w1/1-sm w1/2-md">
            <div>
                <div class="img wFill h4/3 fade img--cover">
                    <img data-src="<?=$community['image']; ?>" alt="" />
                </div>
            </div>
            <div>
                <div class="pad">
                    <?php if (!empty($community['tags'])) { ?>
                        <span class="bdg"><?=implode('</span><span class="bdg">', $community['tags']);?></span>
                    <?php } ?>
                </div>
                <div class="BM txtC pad">
                    <h2><?=Format::htmlspecialchars($community['title']); ?></h2>
                    <?php if (!empty($community['subtitle'])) { ?>
                        <p class="description">
                            <?=Format::htmlspecialchars($community['subtitle']); ?>
                        </p>
                    <?php } ?>
                </div>
            </div>
        </a>
    <?php } ?>
</div>