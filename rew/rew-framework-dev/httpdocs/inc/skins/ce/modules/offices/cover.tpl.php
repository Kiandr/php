<?php if (!empty($offices) && is_array($offices)) { ?>
<div class="columns" id="<?=$this->getUID(); ?>">
    <?php foreach ($offices as $office) { ?>
        <a href="/offices.php?oid=<?=$office['id']; ?>" class="hero hero--landscape column -width-1/2 -width-1/1@md -width-1/1@sm -width-1/1@xs hero--portrait@xs">
            <div class="hero__fg">
                <div class="hero__head">
                    <div class="divider">
                        <span class="divider__label -left -text-upper -text-xs"><?=$office['city']; ?>, <?=$office['state']; ?></span>
                    </div>
                </div>
                <div class="hero__body -flex">
                    <div class="-bottom">
                        <h2 class="-text-upper -text-bold -mar-bottom-0"><?=$office['title']; ?></h2>
                        <p class="-mar-vertical-0"><?=$office['location']; ?></p>
                        <?php if ($office['description']) { ?>
                        <p class="-mar-top-sm -text-xs -text-upper"><?=$office['description']; ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="hero__bg">
                <div class="cloak cloak--dusk"></div>
                <?php if (!empty($office['image'])) { ?>
                    <img class="hero__bg-content" alt="Photo of <?=$office['title']; ?> office" data-src="<?=str_replace('thumbs/380x285/f','thumbs/594x594/', $office['image']);?>" src="/img/util/35mm_landscape.gif"

alt="">
                <?php } ?>
            </div>
        </a>
    <?php } ?>
</div>
<?php } ?>
