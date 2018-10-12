<div class="bar">
    <div class="bar__title"><?= __('Lenders'); ?></div>
    <div class="bar__actions">
        <?php if($can_edit) { ?>
        <a href="/backend/lenders/add/" class="bar__action"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use></svg></a>
        <?php } ?>
    </div>
</div>


<?php if (!empty($lenders)) { ?>

<div class="nodes">
    <ul class="nodes__list">

        <?php foreach ($lenders as $lender) { ?>
        <li class="nodes__branch">
            <div class="nodes__wrap">
                <div class="article">
                    <div class="article__body">
                        <div class="article__thumb"><img src="/thumbs/48x48/<?=(!empty($lender['image']) ? 'uploads/' . $lender['image'] : 'uploads/agents/na.png'); ?>" alt=""></div>
                        <div class="article__content">
                            <a class="text text--strong" href="lender/summary/?id=<?=$lender['id']; ?>"><?=Format::htmlspecialchars($lender['name']); ?></a>
                        </div>
                    </div>
                </div>
                <div class="nodes__actions">
                    <?php if (!empty($can_delete)) { ?>
                        <a class="btn btn--ghost btn--ico" href="lender/delete/?id=<?=$lender['id']; ?>"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-trash"></use></svg></a>
                    <?php } ?>
                </div>
            </div>
        </li>
        <?php } ?>

    </ul>

</div>


    <?php

        // Pagination
        if (!empty($pagination['links'])) {
            echo '<div class="rewui nav_pagination">';
            if (!empty($pagination['prev'])) echo '<a href="' . $pagination['prev']['url'] . '" class="prev">&lt;&lt;</a>';
            if (!empty($pagination['links'])) {
                foreach ($pagination['links'] as $link) {
                    echo '<a href="' . $link['url'] . '"' . (!empty($link['active']) ? ' class="current"' : '') . '>' . $link['link'] . '</a>';
                }
            }
            if (!empty($pagination['next'])) echo '<a href="' . $pagination['next']['url'] . '" class="next">&gt;&gt;</a>';
            echo '</div>';
        }

    ?>

<?php } else { ?>

    <div class="block">
        <p class="block"><?= __('There are currently no available lenders.'); ?></p>
    </div>

<?php } ?>