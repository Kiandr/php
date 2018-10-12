<?php if (!empty($community)) { ?>
	<div class="bdx-community-property-info">

		<div class="bdx-cta-title">
				<h5>See Homes &amp; Specs in <?=$community['SubdivisionName']; ?></h5>
		</div>

		<div class="bdx-cta-body">

			<div class="bdx-properties-photo-contain">
				<div class="bdx-properties-photo">
					<?php if (!empty($results) && is_array($results)) {
						$rand = array_rand($results);

						if (!empty($results[$rand]['Image'])) { ?>
							<img alt="<?=$results[$rand]['Alt'];?>" src="/img/util/35mm_landscape.gif" data-src="<?=$results[$rand]['Image'];?>">
						<?php } else { ?>
							<img src="/builders/res/img/35mm_landscape.gif" data-src="/builders/res/img/404.gif">
						<?php } ?>
					<?php } ?>
				</div>
			</div>

			<div class="bdx-info">
				<div class="btnset">
					<a class="btn scroll-to" href="#community-listings">Jump to Properties!</a>
				</div>
			</div>
		</div>
	</div>
<?php } ?>