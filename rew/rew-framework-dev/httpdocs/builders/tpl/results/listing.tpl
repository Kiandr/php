<?php

// Result Details
$details = array();
if (!empty($result['Bedrooms']))	$details[] = $result['Bedrooms'] . ' beds';
if (!empty($result['Baths']))		$details[] = rtrim($result['Baths'], '.0') . ' baths';
if (!empty($result['BaseSqft']))	$details[] = number_format($result['BaseSqft']) . ' ft<sup>2</sup>';
?>

<div class="bdx-listing-item" listing-id="<?=$result['ListingID'];?>">
	<a rel="nofollow" data-tracker="['Click', '<?=$result['TrackingID'];?>']" class="item-image listing-link" href="<?=$result['Link'];?>">
		<img alt="<?=$result['Alt'];?>" src="/img/util/35mm_landscape.gif" data-src="<?=$result['Image'];?>">
	</a>

	<div class="details">

		<header class="title">
			<h3><a data-tracker="['Click', '<?=$result['TrackingID'];?>']" href="<?=$result['Link'];?>"><?=htmlspecialchars($result['PlanName']); ?></a></h3>
			<span class="pricerange">from $<?=number_format($result['BasePrice']); ?></span>
		</header>

		<div class="data">
			<p class="var name">
				<strong><?=htmlspecialchars($result['SubdivisionName']); ?></strong>
				<span>by 
					<?php if(!empty($result['BrandNameLink'])) { ?>
						<a href="<?=$result['BrandNameLink'];?>"><?=htmlspecialchars($result['BrandName']); ?></a>
					<?php } else { ?>
						<?=htmlspecialchars($result['BrandName']); ?>
					<?php } ?>
				</span>
			</p>
			<p class="var plan"><?=htmlspecialchars($result['PlanType']); ?>, <?=$result['ListingType']; ?></p>
			<p class="var desc"><?=implode(', ', $details); ?></p>
			<p class="var address">
				<?=htmlspecialchars($result['ListingAddress']); ?> <?=htmlspecialchars(implode(', ' , array_filter(array($result['ListingCity'], $result['ListingState']))) . ' ' . $result['ListingZIP']); ?>
			</p>
		</div>
	</div>
</div>
