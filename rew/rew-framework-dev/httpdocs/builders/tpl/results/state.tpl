<div class="bdx-state-item">
	<a href="<?=$result['Link'];?>">

		<div class="images">
			<?php if (!empty($result['Images']) && is_array($result['Images'])) { ?>
				<?php foreach ($result['Images'] as $image) { ?>
					<div class="item-image">
						<img data-src="<?=$image;?>" src="/builders/res/img/35mm_landscape.gif">
					</div>
				<?php } ?>
			<?php } else { ?>
				<div class="item-image">
					<img data-src="/builders/res/img/no-image.gif" src="/builders/res/img/35mm_landscape.gif">
				</div>
			<?php } ?>
		</div>

		<div class="details">

			<header class="title">
				<h2><?=$result['State']; ?></h2>
			</header>

			<div class="info">
				<p>
					<strong><?=$result['Listings'];?> New Homes</strong>
					<span>in <?=$result['Communities'];?> Communities</span>
				</p>
			</div>
		</div>
	</a>
</div>
