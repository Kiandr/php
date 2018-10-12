<?php if (!empty($pagination['links'])) { ?>
    <div class="pagination">
        <?php if (!empty($pagination['prev'])) { ?>
            <a class="prev" rel="prev" href="<?=$pagination['prev']['url']; ?>">
                <svg class="icon--left-arrow">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/inc/skins/ce/img/assets.svg#icon--left-arrow"></use>
                </svg>
                <?= __('Prev'); ?>
            </a>
        <?php } ?>
        <?php foreach ($pagination['links'] as $link) { ?>
            <a class="pagination__link<?=!empty($link['active']) ? ' current' : ''; ?>" href="<?=$link['url']; ?>"><?=$link['link']; ?></a>
        <?php } ?>
        <?php if (!empty($pagination['next'])) { ?>
            <a class="next" rel="next" href="<?=$pagination['next']['url']; ?>">
                <?= __('Next'); ?>
                <svg class="icon--right-arrow">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/inc/skins/ce/img/assets.svg#icon--right-arrow"></use>
                </svg>
            </a>
        <?php } ?>
    </div>
<?php } ?>

