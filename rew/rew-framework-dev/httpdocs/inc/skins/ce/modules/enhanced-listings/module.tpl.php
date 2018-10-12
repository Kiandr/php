<?php
// Display feed links
if (!empty($mlsFeeds) && is_array($mlsFeeds)) {
    if (count($mlsFeeds) > 1) {
        echo '<div><ul class="enhanced-feed-select -text-center">';
        $first = true;
        foreach ($mlsFeeds as $feed => $value) { ?>
            <li class="button button--pill enhanced -mar-top-xs <?=($first) ? '-is-current' : '';?>" data-value="<?=$feed; ?>">
                <a class="-pad-sm" data-value="<?=$feed; ?>"><?=$value['title']; ?></a>
            </li>
        <?php
            $first = false;
        }
        echo '</ul></div>';
    }
}

if (!empty($tab_content) && is_array($tab_content)) {
    foreach ($tab_content as $key => $tab) { ?>
        <div class="tab--content tab--content--<?= $tab['feed']; ?> <?= (!($key == key($mlsFeeds))) ?: 'hidden'; ?>">
            <?=$tab['category_html']; ?>
            <?php Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(true, $tab['feed']); ?>
        </div>
        <?php
    }
}
