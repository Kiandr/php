<?php $skin = ($this instanceof Module) ? $this->getContainer()->getPage()->getSkin() : $this->getSkin(); ?>
<article class="media listing has-actions <?=!empty($bookmarked[$result['ListingMLS']]) ? ' saved' : ''; ?>" id="listing-<?=$result['ListingMLS']; ?>">
	<div class="mediaImg">
		<a href="<?=$result['url_details']; ?>" class="info-links">
			<img data-src="<?=IDX_Feed::thumbUrl($result['ListingImage'], IDX_Feed::IMAGE_SIZE_MEDIUM); ?>" src="/img/util/35mm_landscape.gif" alt="Photo of Listing #<?=($result['idx'] == 'cms' ? $result['ListingMLSNumber'] : $result['ListingMLS']); ?>">
		</a>
		<?php

			// Compliance Auction Banner Display
			if (is_callable($_COMPLIANCE['is_up_for_auction']) && $_COMPLIANCE['is_up_for_auction']($result)) {
				echo '<span class="flag flag-auction hidden"><em>AUCTION</em></span>';

			// Price Reduced
			} else if (!is_null($result['ListingPriceOld']) && $result['ListingPrice'] < $result['ListingPriceOld'] && $_COMPLIANCE['flags']['hide_price_reduction'] != true) {
				echo '<span class="flag flag-reduced hidden"><em>REDUCED</em></span>';

			// New Listing
			} else if (!is_null($result['ListingDOM']) && $result['ListingDOM'] <= 7) {
				echo '<span class="flag flag-new hidden"><em>NEW</em></span>';

			}

		?>
	</div>
	<div class="mediaBody">
		<?php if (isset($result['ListingFeed'])) \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->load($result['ListingFeed']); ?>
		<?php if(isset($_COMPLIANCE['results']['provider_first']) && $_COMPLIANCE['results']['provider_first']($result)) {
			echo '<p class="provider">';
			\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result);
			echo '</p>';
		} ?>
		<hgroup>
			<h3><?=Format::htmlspecialchars($result['Address']); ?></h3>
			<p><?=!empty($result['ListingPrice']) ? '$' . Format::number($result['ListingPrice']) : '&nbsp;'; ?></p>
		</hgroup>
		<p>
		<?php

			// City, State
			echo Format::htmlspecialchars($result['AddressCity']) . ', ' . Format::htmlspecialchars($result['AddressState']) . '<br>';

			// Basic Details
			$details = array();
			if ($result['NumberOfBedrooms']		> 0) $details[] = $result['NumberOfBedrooms'] . ' Bed';
			if ($result['NumberOfBathrooms']	> 0) $details[] = Format::fraction($result['NumberOfBathrooms']) . ' Bath';
			if ($result['NumberOfSqFt']			> 0) $details[] = $result['NumberOfSqFt'] . ' Sqft';
			if ($result['NumberOfAcres']		> 0) $details[] = (float) $result['NumberOfAcres'] . ' ' . Format::plural($result['NumberOfAcres'], 'Acres', 'Acre');
			echo !empty($details) ? implode(', ', $details) . '<br>' : '';

			// Listing Type
			echo Format::htmlspecialchars($result['ListingType']) . '<br>';

			// Show Provider results
			if(empty($_COMPLIANCE['results']['provider_first'])){
				echo \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result);
			 }
			?>
		</p>
		<?php if (!empty($_COMPLIANCE['results']['show_mls'])) { ?>
			<p><?=Lang::write('MLS_NUMBER') . ($result['idx'] == 'cms' ? $result['ListingMLSNumber'] : $result['ListingMLS']); ?></p>
		<?php } ?>
	</div>
	<div class="mediaActions">
		<a class="action-info" href="<?=$result['url_details'];  ?>" title="View more information about <?=Format::htmlspecialchars($result['Address']); ?>."><img src="<?=$skin->getUrl(); ?>/img/ico-info.png" alt=""></a>
		<?php if ($result['idx'] != 'cms') { ?>
			<a class="action-save" rel="nofollow" onclick="$('#listing-<?=$result['ListingMLS']; ?>').Favorite({'feed':'<?=$result['idx']; ?>','mls':'<?=$result['ListingMLS']; ?>'});" title="Save this Property"
			data-event='favorite' <?php $tracking = (IDX_Compliance::FormatListingTrackable($result)); foreach ($tracking as $service => $listing) { echo "data-listing-" . $service . "='" . Format::htmlspecialchars(json_encode($listing)) . "'"; } ?>>
				<img src="<?=$skin->getUrl(); ?>/img/ico-star.png" alt="">
			</a>
		<?php } ?>
	</div>
</article>
