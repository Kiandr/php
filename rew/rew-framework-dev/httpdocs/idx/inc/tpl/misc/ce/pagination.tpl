<?php if (!empty($pagination['links'])) { ?>
    <div class="pagination">
        <?php if (!empty($pagination['prev'])) { ?>
            <a class="prev" rel="prev" href="<?=$pagination['prev']['url']; ?>">
                <svg class="icon--left-arrow">
                    <title>Previous page link</title>
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/inc/skins/ce/img/assets.svg#icon--left-arrow"></use>
                </svg>
                <span>Prev</span>
            </a>
        <?php } ?>
        <?php foreach ($pagination['links'] as $link) { ?>
            <a title="<?=!empty($link['active']) ? 'Currently on page' : 'Page'; ?> <?=$link['link']; ?> of <?=$pagination['pages']?>" class="pagination__link<?=!empty($link['active']) ? ' current' : ''; ?>" href="<?=$link['url']; ?>"><?=$link['link']; ?></a>
        <?php } ?>
        <?php if (!empty($pagination['next'])) { ?>
            <a class="next" rel="next" href="<?=$pagination['next']['url']; ?>">
                <span>Next</span>
                <svg class="icon--right-arrow">
                    <title>Next page link</title>
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/inc/skins/ce/img/assets.svg#icon--right-arrow"></use>
                </svg>
            </a>
        <?php } ?>
    </div>
<?php } ?>