<?php

// Module Disabled
if (empty(Settings::getInstance()->MODULES['REW_FEATURED_COMMUNITIES'])) return;

// No communities added
if (empty($communities)) return;

// Image sizes
$size_image = '352x240/f';

?>
<div class="articleset community-results">
	<?php foreach ($communities as $i => $community) { ?>
		<?php $descript	= Format::stripTags($community['description']); ?>
		<?php $length	= strlen($community['description']); ?>
		<?php $short	= substr($descript, 0, $max); ?>
		<?php $more		= substr($descript, strlen($short)); ?>
		<?=($i === 0 || $i % 2 === 0 ? '<div class="grid_12 row">' : ''); ?>
			<div class="x3">
				<h3><?=Format::htmlspecialchars($community['title']); ?></h3>
				<?php if (!empty($community['description'])) { ?>
					<div class="description">
						<?=Format::htmlspecialchars($community['description']); ?>
					</div>
				<?php } ?>
				<?php if (!empty($community['url'])) { ?>
					<a class="buttonstyle colored-bg mini" href="<?=$community['url']; ?>">View Community</a>
				<?php } ?>
			</div>
			<?php if (!empty($community['image'])) { ?>
				<div class="x3<?=($i % 2 === 1 ? ' last' : ''); ?>">
					<div class="community-image">
						<?=(!empty($community['url']) ? '<a href="' . $community['url'] . '">' : ''); ?>
						<img src="<?=str_replace('/' . $thumbnails . '/', '/' . $size_image . '/', $community['image']); ?>">
						<?=(!empty($community['url']) ? '</a>' : ''); ?>
					</div>
				</div>
			<?php } ?>
		<?=($i % 2 === 1 || $i === count($communities) -1 ? '</div>' : ''); ?>
	<?php } ?>
</div>