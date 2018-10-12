<div id="<?=$this->getUID(); ?>" class="gallery">
	<div class="slideset">
		<?php foreach ($slideshow as $index => $image) { ?>
			<div class="slide">
				<?php if (!empty($image['link'])) echo '<a href="' . Format::htmlspecialchars($image['link']) . '">'; ?>
				<picture>
				    <source media="(max-width:480px)" srcset="/thumbs/375x312/f<?=Format::htmlspecialchars($image['image']); ?>"></source>
				    <source media="(max-width:480px)" srcset="/thumbs/750x624/f<?=Format::htmlspecialchars($image['image']); ?> 2x"></source>
				    <img src="<?=($index == 0 ? Format::htmlspecialchars($image['image']) : '/img/util/slideshow.png'); ?>" <?=($index != 0 ? 'data-src="' . Format::htmlspecialchars($image['image']) . '"' : ''); ?> alt="">
				</picture>
				<?php if (!empty($image['caption'])) { ?>
					<span class="caption">
						<?=Format::htmlspecialchars($image['caption']); ?>
					</span>
				<?php } ?>
				<?php if (!empty($image['link'])) echo '</a>'; ?>
			</div>
		<?php } ?>

	</div>
	<div class="pagination"></div>
</div>