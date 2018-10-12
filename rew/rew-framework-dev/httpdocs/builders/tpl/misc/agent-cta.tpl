<?php if (!empty($agent)) { ?>
	<div class="bdx-agent-info">

		<div class="bdx-cta-title">
			<?php if ($app->page_name == 'community') { ?>
				<h5>Ask Us About this Community Now!</h5>
			<?php } else { ?>
				<h5>Contact Us About this Property Today!</h5>
			<?php } ?>
		</div>

		<div class="bdx-cta-body">

			<div class="bdx-agent-photo-contain">
				<div class="bdx-agent-photo">
					<?php if (!empty($agent['image'])) { ?>
						<img src="/builders/res/img/35mm_landscape.gif" data-src="/thumbs/150x150/uploads/agents/<?=$agent['image'];?>">
					<?php } else { ?>
						<img src="/builders/res/img/35mm_landscape.gif" data-src="/builders/res/img/404.gif">
					<?php } ?>
				</div>
			</div>

			<div class="bdx-info">
				<h3 class="bdx-name"><?=$agent['first_name'];?> <?=$agent['last_name'];?></h3>
				<span class="bdx-title"><?=$agent['title'];?></span>
				<span class="bdx-phone"><?=$agent['cell_phone'];?></span>

				<?php if ($app->page_name == 'community' && !empty($community)) {
					$inquiry_url = '/builders/community-inquire/?community_id=' . $community['SubdivisionID'] . '&agent=' . $agent['id'];
				} else {
					$inquiry_url = '/builders/listing-inquire/?listing_id=' . $listing['id'] . '&agent=' . $agent['id'];
				} ?>

				<div class="btnset">
					<a class="btn popup" href="<?=$inquiry_url;?>">Contact Agent</a>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
