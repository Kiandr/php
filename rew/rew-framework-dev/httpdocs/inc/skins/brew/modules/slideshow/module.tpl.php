<div id="<?=$this->getUID(); ?>" class="gallery">
	<div class="slideset">
		<?php foreach ($slideshow as $image) { ?>
			<?php if (!empty($image['link'])) { ?>
				<a class="slide" href="<?=$image['link']; ?>"><img src="/img/util/slideshow.png" data-src="<?=$image['image']; ?>" alt=""></a>
			<?php } else {?>
				<div class="slide"><img src="/img/util/slideshow.png" data-src="<?=$image['image']; ?>" alt=""></div>
			<?php } ?>
		<?php } ?>
		<img alt="" src="/img/util/slideshow.png" class="ph">
	</div>
</div>