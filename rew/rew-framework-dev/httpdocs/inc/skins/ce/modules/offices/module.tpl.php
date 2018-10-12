<?php  if (!empty($offices)) { ?>
    <div class="columns">
        <?php foreach ($offices as $office) { ?>
        <a href="/offices.php?oid=<?=$office['id']; ?>" class="hero hero--landscape column <?=$this->config('responsive-classes') ? $this->config('responsive-classes') : '-width-1/2 -width-1/2@md -width-1/1@sm -width-1/1@xs hero--portrait@xs';?>">
			<div class="hero__fg">
				<div class="hero__head">
					<div class="divider">
            <span class="divider__label -left -text-upper -text-xs">
                <?php $seperator = !empty($office['city']) && !empty($office['state']) ? ',' : ''; ?>
                <span class="divider__label -left -text-upper -text-xs"><?=Format::htmlspecialchars($office['city']); ?> <?=$seperator ?> <?=Format::htmlspecialchars($office['state']); ?></span>
            </span>
					</div>
				</div>
				<div class="hero__body -flex">
    				<div class="-bottom">
					    <h2 class="-text-upper -text-bold -mar-bottom-0"><?=Format::htmlspecialchars($office['title']); ?></h2>
                        <p class="-mar-vertical-0"><?=Format::htmlspecialchars($office['location']); ?></p>
                        <p class="-mar-top-sm -text-xs -text-upper"><?= Format::htmlspecialchars($office['description']); ?></p>
    				</div>
				</div>
			</div>
			<div class="hero__bg">
				<div class="cloak cloak--dusk"></div>
                        <img class="hero__bg-content" alt="Photo of <?=Format::htmlspecialchars($office['title']); ?> office" data-src="<?=str_replace('thumbs/380x285/f','thumbs/594x594/', $office['image']);?>" src="/img/util/35mm_landscape.gif"

 alt="">
			</div>
        </a>
        <?php } ?>
    </div>
<?php } ?>