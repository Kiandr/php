<?php

// Featured community CTA
if (!empty($communities)) {
	echo '<div class="col width-2/5">';
	foreach ($communities as $community) {
		$url = $community['url'] ?: $community['search_url'];

?>
<a href="<?=$url; ?>">
	<div class="photo photo-bordered text-center">
		<?php if (!empty($community['image'])) { ?>
			<img data-src="<?=$community['image']; ?>" alt="">
		<?php } ?>
		<div class="body">
			<h4><?=Format::htmlspecialchars($community['title']); ?></h4>
			<?php if (!empty($community['tags'])) { ?>
				<div class="tagset"><?=Format::htmlspecialchars(implode(', ', $community['tags'])); ?></div>
			<?php } else { ?>
				<div class="tagset">&nbsp;</div>
			<?php } ?>
		</div>
	</div>
</a>
<?php
	}
	echo '</div>';
}