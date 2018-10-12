<?php

// Featured community CTA
if (!empty($communities)) {
	foreach ($communities as $community) {

?>
<div class="hero">
	<?php if (!empty($community['image'])) { ?>
		<img class="defer" data-src="<?=$community['image']; ?>" alt="">
	<?php } ?>
	<div class="wrap">
		<div class="spacer ratio-1/8"></div>
		<h1><?=Format::htmlspecialchars($community['title']); ?></h1>
		<p><?=Format::htmlspecialchars($community['description']); ?></p>
		<?php if (!empty($community['tags'])) { ?>
			<p class="tagset">
				<?php foreach ($community['tags'] as $tag) { ?>
					<a href="/communities.php?search_keyword=<?=Format::htmlspecialchars($tag); ?>">
						<?=Format::htmlspecialchars($tag); ?>
					</a>
				<?php } ?>
			</p>
		<?php } ?>
		<div class="spacer ratio-1/8"></div>
	</div>
</div>
<?php

	}
}