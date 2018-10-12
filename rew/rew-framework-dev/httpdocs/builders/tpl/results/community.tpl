<div class="bdx-community-item">
	<div class="image-container">
		<a rel="nofollow" data-tracker="['Click', '<?=$result['TrackingID'];?>']" class="item-image community-link" href="<?=$result['Link'];?>">
			<img alt="<?=$result['Alt'];?>" src="/img/util/35mm_landscape.gif" data-src="<?=$result['Image'];?>">
		</a>
	</div>

	<div class="details">
		<header class="title">
			<h3><a data-tracker="['Click', '<?=$result['TrackingID'];?>']" href="<?=$result['Link'];?>"><?=$result['SubdivisionName']; ?></a></h3>
			<span class="pricerange">from $<?=number_format($result['PriceFrom']); ?> &ndash; $<?=number_format($result['PriceTo']); ?></span>
		</header>

		<div class="data">
			<p class="val address"><?=htmlspecialchars($result['City'] . ', ' . $result['State'] . ' ' . $result['Zip']); ?></p>
			<p class="val desc"><?=substr($description = htmlspecialchars(strip_tags($result['SubDescription'])), 0, 140); ?><?=(strlen($description) > 140 ? '...' : '');?></p>
		</div>

		<?php if (!empty($result['Listings'])) { ?>
			<div class="btnset">
				<a rel="nofollow" data-tracker="['Click', '<?=$result['TrackingID'];?>']" class="btn" href="<?=$result['Link'];?>"><?=$result['Listings'];?> matching homes</a>
			</div>
		<?php } ?>

	</div>
</div>
