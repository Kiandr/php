<?php

// Module Disabled
if (empty(Settings::getInstance()->MODULES['REW_FEATURED_COMMUNITIES'])) return;

// Featured Community
$community = array_shift($communities);

// Could Not Find Featured Community
if (empty($community)) {
	echo '<p>The selected featured community could not be located.</p>';
	return;
}

?>
<div id="<?=$this->getUID() ; ?>">
	<h1><?=$community['title']; ?></h1>
	<h2><?=$community['subtitle']; ?></h2>
	<div class="x6 o0">
		<div class="description"><?=$community['description']; ?></div>
		<?php if (!empty($community['stats'])) { ?>
			<h2 class="heading"><?=$community['stats_heading']; ?></h2>
			<table>
				<tr>
					<td><?=$community['stats_total']; ?>:</td>
					<td class="stats"><?=Format::number($community['stats']['total']); ?></td>
				</tr>
				<tr class="even">
					<td><?=$community['stats_average']; ?>:</td>
					<td class="stats">$<?=Format::number($community['stats']['average']); ?></td>
				</tr>
				<tr>
					<td><?=$community['stats_highest']; ?>:</td>
					<td class="stats">$<?=Format::number($community['stats']['max']); ?></td>
				</tr>
				<tr class="even">
					<td><?=$community['stats_lowest']; ?>:</td>
					<td class="stats">$<?=Format::number($community['stats']['min']); ?></td>
				</tr>
			</table>
		<?php } ?>
	</div>
	<div class="x6 o6">
		<div class="community-image">
			<?php if (!empty($community['images'])) { ?>
				<?php foreach ($community['images'] as $i => $image) { ?>
					<img data-src="<?=$image; ?>" src="<?=$placeholder; ?>" class="image<?=$i; ?><?=($i === 0) ? '' : ' hidden'; ?>">
				<?php } ?>
			<?php } ?>
		</div>
		<div class="community-thumbnails">
			<?php if (!empty($community['images'])) { ?>
				<?php foreach ($community['images'] as $i => $image) { ?>
					<a href="#image<?=$i; ?>"><img data-src="<?=$image; ?>" src="<?=$placeholder; ?>"></a>
				<?php } ?>
			<?php } ?>
		</div>
		<p>Roll over thumbnail to enlarge.</p>
	</div>
	<div class="community-links">
		<span><a href="<?=$community['anchor_one_link']; ?>"><?=$community['anchor_one_text']; ?></a></span>
		<span><a href="<?=$community['anchor_two_link']; ?>"><?=$community['anchor_two_text']; ?></a></span>
	</div>
</div>