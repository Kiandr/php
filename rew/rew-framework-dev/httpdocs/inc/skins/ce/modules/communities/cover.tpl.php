<?php if (!empty($communities) && is_array($communities)) { ?>
    <div class="columns" id="<?=$this->getUID(); ?>">
        <?php foreach ($communities as $community) { ?>
            <?php if(!empty($community['url'])) { ?>
            <a href="<?=htmlspecialchars($community['url']);?>" class="hero hero--portrait column -width-1/4 -width-1/2@md -width-1/1@sm -width-1/1@xs">
            <?php } else { ?>
            <div class="hero hero--portrait column -width-1/4 -width-1/2@md -width-1/1@sm -width-1/1@xs">
            <?php } ?>
                <div class="hero__fg">
                    <div class="hero__body -flex">
                        <div class="-bottom">
                        <?php if ($community['subtitle']) { ?>
                            <span class="-font-fantasy -mar-bottom-xs"><?=Format::truncate(htmlspecialchars($community['subtitle']), 32); ?></span>
                        <?php } ?>
                        <?php if ($community['title']) { ?>
                            <h2 class="-text-upper"><?=Format::truncate(htmlspecialchars($community['title']), 32); ?></h2>
                        <?php } ?>
                        <?php if ($community['description']) { ?>
                            <p class="-text-xs -text-upper"><?=Format::truncate($community['description'], 128); ?></p>
                        <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="hero__bg">
                    <div class="cloak cloak--dusk"></div>
                    <?php if (!empty($community['image'])) { ?>
                        <img class="hero__bg-content" data-src="<?=str_replace('thumbs/x700','thumbs/428x428', $community['image']);?>" data-srcset="<?=str_replace('thumbs/x700','thumbs/856x856', $community['image']);?> 2x" src="/img/util/35mm_landscape.gif" alt="Photo of <?=$community['title']?> community">
                    <?php } ?>
                </div>
            <?php if(!empty($community['url'])) { ?>
            </a>
            <?php } else { ?>
            </div>
            <?php } ?>
        <?php } ?>
    </div>
<?php } ?>
