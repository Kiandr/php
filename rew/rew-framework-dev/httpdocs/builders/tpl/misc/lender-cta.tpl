<?php if (!empty($lender)) { ?>
	<div class="bdx-lender-info">

		<div class="bdx-cta-title">
			<h5>Get Pre-Approved for a Mortgage</h5>
		</div>

		<div class="bdx-cta-body">

			<div class="bdx-lender-photo-contain">
				<div class="bdx-lender-photo">
					<?php if (!empty($lender['file'])) { ?>
						<img src="/builders/res/img/35mm_landscape.gif" data-src="/thumbs/150x150/uploads/<?=$lender['file'];?>">
					<?php } else { ?>
						<img src="/builders/res/img/35mm_landscape.gif" data-src="/builders/res/img/404.gif">
					<?php } ?>
				</div>
			</div>

			<div class="bdx-info">
				<h3 class="bdx-name"><?=$lender['first_name'];?> <?=$lender['last_name'];?></h3>
				<span class="bdx-phone"><?=$lender['cell_phone'];?></span>

				<?php if ($app->page_name == 'community' && !empty($community)) {
					$inquiry_url = '/builders/community-inquire/?community_id=' . $community['SubdivisionID'] . '&lender=' . $lender['id'];
				} else {
					$inquiry_url = '/builders/listing-inquire/?listing_id=' . $listing['id'] . '&lender=' . $lender['id'];
				} ?>

				<div class="btnset">
					<a class="btn popup" href="<?=$inquiry_url;?>">Contact Lender</a>
				</div>

			</div>
		</div>
	</div>
<?php } ?>