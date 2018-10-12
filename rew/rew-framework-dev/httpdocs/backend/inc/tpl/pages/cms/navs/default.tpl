<div class="bar">
    <span class="bar__title"><?= __('Navigation'); ?></span>
</div>

<div class="nodes">
    <ul class="nodes__list">
        <?php if (is_array($navs) && !empty($navs)) {?>
            <?php foreach ($navs AS $nav) {?>
                <li class="nodes__branch">
                    <div class="nodes__wrap">
                        <div class="article">
                            <div class="article__body">
                                <div class="article__thumb thumb thumb--medium -bg-rew2"><svg class="icon icon--invert"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-link"></use></svg></div>
                                <div class="article__content">
                                    <a class="text text--strong" href="<?=$nav['link'] ?>"><?=htmlspecialchars($nav['name']); ?></a>
                                    <div class="text text--mute"><?=htmlspecialchars($nav['description']); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            <?php } ?>
        <?php } ?>
    </ul>
</div>
