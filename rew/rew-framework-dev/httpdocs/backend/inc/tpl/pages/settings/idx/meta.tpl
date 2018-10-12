<?php

/**
 * @var array $metaData
 * @var array $metaInfo
 * @var string $idxFeed
 * @var string $placeholderUrl
 * @var string $listingLink
 * @var string $listingUrl
 * @var array $listingTags
 * @var array $listingUrlTags
 */

?>
<form action="?submit" method="post" class="rew_check">
    <input type="hidden" name="feed" value="<?=$idxFeed; ?>">

    <div class="bar">
        <div class="bar__title"><?= __('IDX Meta Information'); ?></div>
    </div>

    <div class="block">

        <ul class="tabs">
            <li><a href="<?=URL_BACKEND; ?>settings/idx/?feed=<?=$idxFeed; ?>"><?= __('General'); ?></a></li>
            <li class="current"><a href="<?=URL_BACKEND; ?>settings/idx/meta/?feed=<?=$idxFeed; ?>"><?= __('Meta'); ?></a></li>
            <?php if ($saved_search_email_responsive_template_exists) { ?>
            <li><a href="<?=URL_BACKEND; ?>settings/idx/savedsearches/?feed=<?=$feed; ?>"><?= __('Saved Searches Email'); ?></a></li>
            <?php } ?>
        </ul>

        <div class="field">
            <label class="field__label"><?= __('Custom Listing URL'); ?></label>
            <input class="w1/1" id="listing-link" name="lang[IDX_LISTING_URL]" value="<?=$listingLink; ?>" placeholder="<?=$placeholderUrl; ?>" data-slugify='{ "specialChars": "{}", "lowercase": false }'>
            <input class="w1/1" id="link-placeholder" value="<?=sprintf($listingUrl, $listingLink); ?>" data-placeholder="<?=sprintf($listingUrl, '{$value$}'); ?>" readonly>
            <label class="field__label">* <?= __('The Listing\'s MLS&reg; Number will always be present at the start of the Listing URL.'); ?></label>
            <label class="field__label"><strong><?= __('Available Tags'); ?>:</strong> {<?=implode('}, {', $listingUrlTags); ?>}</label>
        </div>

        <?php

            // Render meta fields
            if (!empty($metaInfo)) {
                foreach ($metaInfo as $metaTitle => $metaFields) {
                    echo sprintf('<div class="divider"><h3 class="divider__label divider__label--left">%s</h3></div>', htmlspecialchars($metaTitle));
                    echo '<div class="cols">';
                    foreach ($metaFields as $metaLabel => $metaField) {
                        $metaValue = $metaData[$metaField];
                        echo '<div class="field col w1/3">';
                        echo sprintf('<label class="field__label">%s</label>', $metaLabel);
                        echo sprintf('<input class="w1/1" name="lang[%s]" value="%s">', $metaField, $metaValue);
                        if ($metaLabel === 'Listing Details') {
                            echo sprintf('<label class="field__label"><strong>' . __('Tags') . ':</strong> {%s}</label>', implode('}, {', $listingTags));
                        }
                        echo '</div>';
                    }
                    echo '</div>';
                }
            }

        ?>
        <div style="margin-bottom: 50px;"></div>
    </div>

    <div class="btns btns--stickyB">
        <div class="R">
            <button class="btn btn--positive" type="submit">
                <svg class="icon icon-check mar0">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use>
                </svg>
                <?= __('Save'); ?>
            </button>
        </div>
    </div>

</form>
