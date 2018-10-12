<?php

// Featured agent CTA
if (!empty($agents)) {
	echo '<div class="col width-2/5 featured-agents">';
	echo '<div class="colset">';
	foreach ($agents as $agent) {

?>
<div class="col width-1-sm width-1/2-md width-1/2-lg width-1/2-xl">
	<div class="photo photo-bordered text-center">
		<a href="<?=$agent['link']; ?>">
			<img data-src="<?=$agent['image']; ?>" alt="">
			<div class="body">
				<h4><?=Format::htmlspecialchars($agent['name']); ?></h4>
				<?php if (!empty($agent['title'])) { ?>
					<div class="tagset"><?=Format::htmlspecialchars($agent['title']); ?></div>
				<?php } ?>
			</div>
		</a>
	</div>
</div>
<?php
	}
	echo '</div>';
	echo '</div>';
}