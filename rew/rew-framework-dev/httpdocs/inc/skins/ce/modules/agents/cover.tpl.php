<?php if(!empty($agents) && is_array($agents)) { ?>
<div class="columns" id="<?=$this->getUID();?>">
    <?php foreach($agents as $agent) { ?>
        <?=sprintf('<%s class="hero hero--portrait column -width-1/4 -width-1/2@md -width-1/1@sm -width-1/1@xs">',
            (!empty($agent['link']) ? 'a href="' . htmlspecialchars($agent['link']) . '"' : 'div tabindex="0"')
        ); ?>
            <div class="hero__fg">
                <?php if(!empty($agent['title'])) { ?>
                <div class="hero__head">
                    <div class="divider">
                        <span class="divider__label -left -text-upper -text-xs"><?=htmlspecialchars($agent['title']); ?></span>
                    </div>
                </div>
                <?php } ?>
                <div class="hero__body -flex">
                    <div class="-bottom">
                        <h2 class="-text-upper -mar-bottom-xs"><?=htmlspecialchars($agent['first_name'] . ' ' . $agent['last_name']); ?></h2>
                        <?php if($agent['remarks']) { ?>
                        <p class="-text-xs -text-upper"><?=Format::stripTags($agent['remarks']); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="hero__bg">
                <div class="cloak cloak--dusk"></div>
                <?php if(!empty($agent['image'])) { ?>
                    <img class="hero__bg-content" data-src="<?=str_replace('thumbs/275x275/r/','thumbs/428x428/r/', $agent['image']);?>" data-srcset="<?=str_replace('thumbs/275x275/r/','thumbs/856x856/r/', $agent['image']);?> 2x" src="/img/util/35mm_landscape.gif"
                    alt="Photo of <?=htmlspecialchars($agent['first_name'] . ' ' . $agent['last_name']); ?>">
                <?php } ?>
            </div>
        <?=(!empty($agent['link']) ? '</a>' : '</div>'); ?>
    <?php } ?>
</div>
<?php } ?>
