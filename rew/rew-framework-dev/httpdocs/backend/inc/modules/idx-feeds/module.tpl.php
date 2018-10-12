<?php

/**
 * @var string $urlQuery
 * @var string $idxFeed
 * @var array $idxFeeds {
 *   @var string $link
 *   @var string $title
 * }
 */
if (empty($idxFeed) || empty($idxFeeds)) return;

?>
<div class="bar bar--feeds">
    <a id="feedSwitcher" class="bar__title bar__title--feeds">
        <?=htmlspecialchars($idxFeeds[$idxFeed]['title']); ?>
        <svg class="icon icon-drop">
            <use xlink:href="/backend/img/icos.svg#icon-drop" xmlns:xlink="http://www.w3.org/1999/xlink"></use>
        </svg>
    </a>
    <ul class="menu--drop menu__list menu__list--feeds hidden">
        <?php foreach ($idxFeeds as $idxLink => $feed) { ?>
            <li class="menu__item">
                <a class="menu__link<?=$idxLink === $idxFeed ? ' is-active' : ''; ?>" href="?feed=<?=$idxLink . $urlQuery; ?>">
                    <?=htmlspecialchars($feed['title']); ?>
                </a>
            </li>
        <?php } ?>
    </ul>
</div>
