<?php

/**
 * @var array $addLinks
 * @var array $navigation
 */

?>

 <!-- Creation Links -->
<?php if (!empty($addLinks) && is_array($addLinks)):?>

    <div class="menu menu--drop hidden" id="menu--add">
        <ul class="menu__list">
            <?php foreach ($addLinks AS $addLink):?>

                <?php if ($addLink['type'] == 'line'):?>
                    <li class="menu__item divider"></li>
                <?php elseif (!empty($addLink['link'])): ?>
                    <li class="menu__item">
                        <a class="menu__link" href="<?=htmlspecialchars($addLink['link']); ?>">
                            <?php if (!empty($addLink['icon'])):?>
                                <svg class="icon icon-<?=htmlspecialchars($addLink['icon']);?>"><use xlink:href="/backend/img/icos.svg#icon-<?=htmlspecialchars($addLink['icon']);?>"/></svg>
                            <?php endif;?>
                            <?=htmlspecialchars($addLink['title']);?>
                        </a>
                     </li>
                 <?php endif;?>
            <?php endforeach;?>
        </ul>
    </div>

    <div class="btns--add-new">
        <a class="btn btn--strong w1/1" data-drop="#menu--add"><svg class="icon icon-snippet"><use xlink:href="/backend/img/icos.svg#icon-add"/></svg><?= __('Add New'); ?></a>
    </div>

<?php endif;?>

<!-- Section Links -->
<nav class="mnu mnu--stacked">
    <ul>
        <?php if (!empty($navigation) && is_array($navigation)):?>
            <?php foreach ($navigation AS $section):?>
                <?php if ($section['type'] == 'header'):?>
                    <li class="dvd"></li>
                    <li class="ttl"><?=htmlspecialchars($section['title']); ?></li>
                <?php elseif ($section['type'] == 'line'):?>
                    <li class="dvd"></li>
                <?php else: ?>
                    <li>
                        <?php if (!empty($section['link'])):?>
                            <?=(!empty($section['link'])
                                ? '<a href="'.htmlspecialchars($section['link']) . '"' . ($section['current'] ? ' class="current"' : '') . '>'
                                : '');?>
                        <?php endif;?>
                        <?php if (!empty($section['icon'])):?>
                            <svg class="icon icon-<?=htmlspecialchars($section['icon']);?>"><use xlink:href="/backend/img/icos.svg#icon-<?=htmlspecialchars($section['icon']);?>"/></svg>
                        <?php endif;?>
                        <?=htmlspecialchars($section['title']);?>
                        <?php if (!empty($section['link'])):?>
                             </a>
                         <?php endif;?>
                     </li>
                <?php endif;?>
            <?php endforeach;?>
        <?php endif;?>
    </ul>
</nav>
