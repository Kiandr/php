<?php if (!empty($entry['thumbnails'])) { ?>

	<div id="gallery" class="business-gallery-container uk-width-small-1-1">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<div class="business-image-gallery">
			        <?php foreach ($entry['thumbnails'] as $count => $photo) { ?>
				        <div><img src="/img/util/35mm_landscape.gif" data-src="/thumbs/800x600<?=$photo; ?>" style="width: 100%"></div>
				    <?php } ?>
				</div>
			</div>
			<div class="uk-width-1-1">
				<div class="business-image-carousel">
				    <?php foreach ($entry['thumbnails'] as $count => $photo) { ?>
						<div><img src="/thumbs/200x150/img/util/35mm_landscape.gif" data-src="/thumbs/200x150<?=$photo;?>" style="width: 150px"></div>
				    <?php } ?>
				</div>
			</div>
		</div>
	</div>
<?php } ?>