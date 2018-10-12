<?php

// Module Disabled
if (empty(Settings::getInstance()->MODULES['REW_FEATURED_COMMUNITIES'])) return;

// Featured Community
$community = array_shift($communities);

// Could Not Find Featured Community
if (empty($community)) return;

// Use photo gallery
$photoGallery = false;
if (!empty($community['images'])) {
    $photoGallery = $this->getContainer()->module('fgallery', [
        'images' => $community['images']
    ])->display(false);
}

?>
<div id="<?=$this->getUID() ; ?>" class="community">

	<div class="hero hero--auto -mar-bottom">
		<div class="hero__fg">
			<div class="hero__body -bottom -text-center -pad-vertical-lg">
				<h2 class="-font-fantasy -mar-bottom-xs"><?=Format::htmlspecialchars($community['subtitle']); ?></h2>
				<h1 class="-text-upper -mar-bottom-xs"><?=Format::htmlspecialchars($community['title']); ?></h1>
				<?php if ($tags = $community['tags']) { ?>
					<p class="-mar-bottom-0"><?=implode(' &bull; ', $community['tags']);?></p>
				<?php } ?>
			</div>
		</div>
		<div class="hero__bg">
			<div class="cloak cloak--dusk"></div>
		</div>
	</div>


<div class="columns -pad-top">

	<div class="column -width-1/2 -width-1/1@sm -width-1/1@md -pad-right">
	<div class="community__desc -pad-vertical">
		<h2 class="page-h2">About <?=Format::htmlspecialchars($community['title']); ?></h2>
		<?php if (!empty($community['description'])) { ?>
			<p class="description">
				<?=$community['description']; ?>
			</p>
		<?php } ?>
	</div>

	<div class="divider -mar-bottom">
		<span class="divider__label -left"><?=Format::htmlspecialchars($community['stats_heading']); ?></span>
	</div>

	<?php if (!empty($community['stats'])) { ?>
	<div class="keyvals -mar-bottom">
		<div class="keyval">
			<strong class="keyval__key"><?=Format::htmlspecialchars($community['stats_total']); ?></strong>
			<span class="keyval__val"><?=Format::number($community['stats']['total']); ?></span>
		</div>
		<div class="keyval">
			<strong class="keyval__key"><?=Format::htmlspecialchars($community['stats_average']); ?></strong>
			<span class="keyval__val">$<?=Format::number($community['stats']['average']); ?></span>
		</div>
		<div class="keyval">
			<strong class="keyval__key"><?=Format::htmlspecialchars($community['stats_highest']); ?></strong>
			<span class="keyval__val">$<?=Format::number($community['stats']['max']); ?></span>
		</div>
		<div class="keyval">
			<strong class="keyval__key"><?=Format::htmlspecialchars($community['stats_lowest']); ?></strong>
			<span class="keyval__val">$<?=Format::number($community['stats']['min']); ?></span>
		</div>

	</div>
	<?php } ?>
	</div>

	<?php if(!strstr($community['images'][0], '404')) { ?>
	<div class="column -width-1/2 -width-1/1@sm -width-1/1@md">
	<?=$photoGallery; ?>
	</div>
	<?php } ?>

</div>

</div>
