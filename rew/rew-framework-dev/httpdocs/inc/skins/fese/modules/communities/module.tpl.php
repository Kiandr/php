<?php

// Module Disabled
if (empty(Settings::getInstance()->MODULES['REW_FEATURED_COMMUNITIES'])) return;

// Featured Community
$community = array_shift($communities);

// Could Not Find Featured Community
if (empty($community)) return;

// Use photo gallery
$photoGallery = false;
if (!empty($community['images'])) {
    $photoGallery = $this->getContainer()->module('fgallery', [
        'images' => $community['images']
    ])->display(false);
}

?>
<div id="<?=$this->getUID() ; ?>">
    <h1><?=Format::htmlspecialchars($community['title']); ?></h1>
    <h2><?=Format::htmlspecialchars($community['subtitle']); ?></h2>
    <?=$photoGallery; ?>
    <div class="cols marB-md">
        <div class="col w1/1 w1/1-sm">
            <h2 class="page-h2">About <?=Format::htmlspecialchars($community['title']); ?></h2>
            <?php if (!empty($community['description'])) { ?>
                <p class="description" style="padding-right: 40px">
                    <?=$community['description']; ?>
                </p>
            <?php } ?>
            <?php if ($tags = $community['tags']) { ?>
                <h3 class="page-h3"><?=Locale::spell('Neighborhood'); ?> Tags</h3>
                <p><?=implode(' &bull; ', $community['tags']);?></p>
            <?php } ?>
        </div>
        <?php if (!empty($community['stats'])) { ?>
            <div class="col w1/1 w1/1-sm">
                <div class="kvs">
                    <h3 class="page-h3"><?=Format::htmlspecialchars($community['stats_heading']); ?></h3>
                    <ul>
                        <li class="kv">
                            <strong class="k"><?=Format::htmlspecialchars($community['stats_total']); ?>:</strong>
                            <span class="v"><?=Format::number($community['stats']['total']); ?></span>
                        </li>
                        <li class="kv">
                            <strong class="k"><?=Format::htmlspecialchars($community['stats_average']); ?>:</strong>
                            <span class="v">$<?=Format::number($community['stats']['average']); ?></span>
                        </li>
                        <li class="kv">
                            <strong class="k"><?=Format::htmlspecialchars($community['stats_highest']); ?>:</strong>
                            <span class="v">$<?=Format::number($community['stats']['max']); ?></span>
                        </li>
                        <li class="kv">
                            <strong class="k"><?=Format::htmlspecialchars($community['stats_lowest']); ?>:</strong>
                            <span class="v">$<?=Format::number($community['stats']['min']); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
