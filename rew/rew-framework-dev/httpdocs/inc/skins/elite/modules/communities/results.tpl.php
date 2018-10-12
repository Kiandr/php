<?php

// Module Disabled
if (empty(Settings::getInstance()->MODULES['REW_FEATURED_COMMUNITIES'])) return;

// No communities added
if (empty($communities)) return;

// Image sizes
$size_image = '352x240/f';

?>
<div class="uk-grid uk-grid-small">
    <?php foreach ($communities as $i => $community) { ?>
        <?php $descript	= Format::stripTags($community['description']); ?>
        <?php $length	= strlen($community['description']); ?>
        <?php $short	= substr($descript, 0, $max); ?>
        <?php $more		= substr($descript, strlen($short)); ?>

        <div class="uk-width-small-1-1 uk-width-medium-1-3 uk-width-large-1-3 uk-margin-bottom">
            <div class="community-container-outer">		
                <?php if (!empty($community['image'])) { ?>	
                <div class="uk-cover-background uk-position-relative community-container-inner" style="background-image: url('<?=$thumbnails ? str_replace('/' . $thumbnails . '/', '/' . $size_image . '/', $community['image']) : '/thumbs/' . $size_image . $community['image']; ?>');">
                    <img width="600" height="400" alt="" src="<?= Format::htmlspecialchars($placeholder); ?>" class="uk-invisible">
                    <a href="<?=$community['url']; ?>" title="<?=Format::htmlspecialchars($community['title']); ?>" class="uk-position-cover uk-flex uk-flex-center uk-flex-bottom community-overlay"></a>
                    <a href="<?=$community['url']; ?>" class="uk-position-bottom uk-text-center community-title"><?=Format::htmlspecialchars($community['title']); ?></a>
                </div>
                <?php } ?>
                <div class="community-container-content">
                    <p><?=Format::truncate(Format::htmlspecialchars($community['description']),130); ?></p>	
                    <p><a href="<?=$community['url']; ?>">Explore <?=Format::htmlspecialchars($community['title']); ?> <i class="uk-icon uk-icon-chevron-right uk-icon-justify"></i></a></p>
                </div>
            </div>
        </div>

    <?php } ?>
</div>
<hr class="uk-article-divider">
