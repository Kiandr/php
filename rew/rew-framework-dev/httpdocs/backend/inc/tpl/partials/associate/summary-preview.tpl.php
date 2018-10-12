<?php

/**
 * Associate preview template
 * @var string $firstName
 * @var string $lastName
 * @var string $image
 * @var int $lastLogon
 */


?>
<div class="block">
    <div class="article">
        <div class="article__body">
            <?php if (empty($image)) { ?>
                <div class="article__thumb thumb thumb--large -bg-<?=strtolower($lastName[0]); ?>">
                    <span class="thumb__label"><?=sprintf('%s%s',
                        $firstName ? $firstName[0] : '',
                        $lastName ? $lastName[0] : ''
                    ); ?></span>
                </div>
            <?php } else { ?>
                <div class="article__thumb thumb thumb--large">
                    <img src="/thumbs/60x60/uploads/associates/<?=urlencode($image) ?: 'na.png'; ?>">
                </div>
            <?php } ?>

            <div class="article__content">
                <div class="text text--strong text--large">
                    <?=Format::htmlspecialchars($firstName . ' ' . $lastName); ?>
                </div>
                <?php if($lastLogon != '0000-00-00 00:00:00' && $lastLogon != NULL) { ?>
                    <?=Format::dateRelative($lastLogon);?>
                <?php } else {
                    echo __('Never Signed In');
                } ?>
            </div>
        </div>
    </div>
</div>