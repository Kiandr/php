<?php


// Get skin instance
$skin = $this instanceof Module ? $this->getContainer()->getPage()->getSkin() : $this->getSkin();
$skin_url = $skin->getUrl();

// Listing MLS #
$ListingMLS = $result['idx'] == 'cms' ? $result['ListingMLSNumber'] : $result['ListingMLS'];

// Check if saved to favorites
$is_bookmarked = !empty($bookmarked[$result['ListingMLS']]);

// Check if user dismissed this property
$is_dismissed = !empty($dismissed[$result['ListingMLS']]);

?>
<article data-listing='<?=json_encode(array('feed' => $result['idx'], 'mls' => $result['ListingMLS'])); ?>' class="col listing
	<?=$is_dismissed ? ' dismissed' : ''; ?>
	<?=$is_bookmarked ? ' saved' : ''; ?>
">
	<header>
	    <h4>
	    	<a href="<?=$result['url_details']; ?>" class="info-links">
	    		<?php if (!empty($result['ListingTitle'])) { ?>
	    			<?=$result['ListingTitle']; ?>
			    <?php } else if (strlen($result['ListingRemarks']) > 50) { ?>
					<?=Format::htmlspecialchars(Format::truncate(ucwords(strtolower($result['ListingRemarks'])), 50)); ?>
			    <?php } else { ?>
			    	<?php if (!empty($result['NumberOfBedrooms'])) echo Format::number($result['NumberOfBedrooms']) . ' Bedroom'; ?>
					<?=Format::htmlspecialchars($result['ListingType']); ?> Property in <?=ucwords(strtolower($result['AddressCity'])); ?>, <?=$result['AddressState']; ?>
			    <?php } ?>
	    	</a>
	    </h4>
	</header>
    <div class="body">
		<div class="photo ratio-3/4">
			<?php

				// Price Reduction
				$reduced = !empty($result['ListingPriceOld']) && ($result['ListingPrice'] < $result['ListingPriceOld']);
				$reduced = $reduced ? abs(round((($result['ListingPrice'] - $result['ListingPriceOld']) / $result['ListingPriceOld']) * 100)) : NULL;

				// Compliance Auction Banner Display
				if (is_callable($_COMPLIANCE['is_up_for_auction']) && $_COMPLIANCE['is_up_for_auction']($result)) {
					echo '<span class="flag flag-auction">AUCTION</span>';
				// Price Reduced
				} else if ($reduced > 0 && $_COMPLIANCE['flags']['hide_price_reduction'] != true) {
					echo '<span class="flag flag-reduced">REDUCED ' . $reduced . '% </span>';

				// New Listing
				} else if (!is_null($result['ListingDOM']) && $result['ListingDOM'] <= 7) {
					echo '<span class="flag flag-new">NEW</span>';

				}

			?>
			<a href="<?=$result['url_details']; ?>" class="info-links"><img data-src="<?=IDX_Feed::thumbUrl($result['ListingImage'], IDX_Feed::IMAGE_SIZE_MEDIUM); ?>" src="/img/util/35mm_landscape.gif" alt="Photo of Listing #<?=($result['idx'] == 'cms' ? $result['ListingMLSNumber'] : $result['ListingMLS']); ?>"></a>
		</div>
		<div class="actions">
			<a rel="nofollow" data-hide='<?=json_encode(array('feed' => $result['idx'], 'mls' => $result['ListingMLS'])); ?>' title="<?=$is_dismissed ? 'Show this Property' : 'Hide this Property'; ?>">
				<i class="icon-listingHide"></i>
			</a>
			<a rel="nofollow" data-save='<?=json_encode(array('feed' => $result['idx'], 'mls' => $result['ListingMLS'])); ?>' title="<?=$is_bookmarked ? 'Remove Saved Property' : 'Save this Property'; ?>"
			data-event='favorite' <?php $tracking = (IDX_Compliance::FormatListingTrackable($result)); foreach ($tracking as $service => $listing) { echo "data-listing-" . $service . "='" . Format::htmlspecialchars(json_encode($listing)) . "'"; } ?>>
				<i class="icon-listingFav"></i>
			</a>
		</div>
		<div class="details">
                <?php if(!empty($_COMPLIANCE['results']['provider_first']) && $_COMPLIANCE['results']['provider_first']($result)) { ?>
                    <?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result); ?>
                <?php } ?>
			<h4 class="val price">
				<?=!empty($result['ListingPrice']) ? '$' . Format::number($result['ListingPrice']) : '&nbsp;'; ?>
				<?=!empty($result['ListingStatus']) ? '<span class="status">' . ucwords($result['ListingStatus']) . '</span>' : ''; ?>
			</h4>
			<?php if (!empty($result['NumberOfBedrooms']) || !empty($result['NumberOfBathrooms']) || !empty($result['NumberOfSqFt']) || $result['NumberOfAcres'] > 0) { ?>
				<ul class="keyvalset">
					<?php if (!empty($result['NumberOfBedrooms'])) { ?>
						<li class="keyval beds">
							<span><?=Format::number($result['NumberOfBedrooms']); ?></span>
							<strong title="Bedrooms">Beds</strong>
						</li>
					<?php } ?>
					<?php if (floatval($result['NumberOfBathrooms']) > 0) { ?>
						<li class="keyval baths">
							<span><?=Format::fraction($result['NumberOfBathrooms']); ?></span>
							<strong title="Bathrooms">Baths</strong>
						</li>
					<?php } ?>
					<?php if ($result['NumberOfAcres'] > 0) { ?>
						<li class="keyval acres">
							<span><?=Format::number($result['NumberOfAcres'], 2) + 0; ?></span>
							<strong>Acres</strong>
						</li>
					<?php } ?>
					<?php if (!empty($result['NumberOfSqFt'])) { ?>
						<li class="keyval sqft">
							<span><?=Format::number($result['NumberOfSqFt']); ?></span>
							<strong title="Square Feet">ft&sup2;</strong>
						</li>
					<?php } ?>
				</ul>
			<?php } else { ?>
				<p class="val">&nbsp;</p>
			<?php } ?>
            <p class="val proptype"><?=$result['ListingType']; ?></p>
			<p class="val adr"><span class="locality"><?=$result['Address']; ?>, <?=ucwords(strtolower($result['AddressCity'])); ?></span>, <?=$result['AddressState']; ?></p>
			<?php if (!empty($ListingMLS)) { ?>
				<p class="val mls"><?=Lang::write('MLS_NUMBER'); ?><?=Format::htmlspecialchars($ListingMLS); ?></p>
			<?php } else { ?>
				<p class="val mls">&nbsp;</p>
			<?php } ?>
			<p class="description"><?=Format::htmlspecialchars(Format::truncate(ucwords(strtolower($result['ListingRemarks'])), 260)); ?></p>
		</div>
		<div class="btnset">
			<a href="<?=$result['url_details']; ?>" class="btn strong">View Listing <i class="icon-chevron-right"></i></a>
		</div>
        <div class="resultIDXCompliance">
            <?php if (isset($result['ListingFeed'])) \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->load($result['ListingFeed']); ?>
            <?php if(empty($_COMPLIANCE['results']['provider_first']) || $_COMPLIANCE['results']['provider_first']($result) == false) { ?>
            <?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result); ?>
            <?php } ?>
        </div>
    </div>
</article>