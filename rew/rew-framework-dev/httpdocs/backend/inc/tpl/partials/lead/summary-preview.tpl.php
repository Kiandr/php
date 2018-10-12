<?php

/**
 * Lead preview template
 * @var string $firstName
 * @var string $lastName
 * @var string $leadId
 * @var string $lastActive
 */

?>
<div class="block">
    <div class="article">
        <div class="article__body lead-details">
            <div class="article__thumb thumb thumb--large -bg-<?=strtolower($lastName[0]); ?>">
                <span class="thumb__label"><?=sprintf('%s%s',
                    $firstName ? $firstName[0] : '',
                    $lastName ? $lastName[0] : ''
                ); ?></span>
            </div>
            <div class="article__content">
                <div class="text text--strong lead-name">
                    <span style="vertical-align: middle;"><?=Format::htmlspecialchars($firstName . ' ' . $lastName); ?></span>
                    <a class="-dIB" href="<?=sprintf('%sleads/lead/edit/?id=%s', URL_BACKEND, $leadId); ?>" title="Edit this lead" style="text-decoration: none;">
                        <svg class="icon icon-pencil"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-pencil"></use></svg>
                    </a>
                </div>
                <?php if ($lastActive) { ?>
                    <div class="text text--mute">
                        <?php if($lastActive != '0000-00-00 00:00:00' && $lastActive != NULL) { ?>
                            <?=Format::dateRelative($lastActive);?>
                        <?php } else { ?>
                            Has Never Been Active
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>