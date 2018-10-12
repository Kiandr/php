<article id="listing-<?=$result['ListingMLS']; ?>" class="listing<?=!empty($bookmarked[$result['ListingMLS']]) ? ' saved' : ''; ?>">
	<a href="<?=$result['url_details']; ?>">
		<div class="photo">
			<img data-src="<?=IDX_Feed::thumbUrl($result['ListingImage'], IDX_Feed::IMAGE_SIZE_MEDIUM); ?>" src="/img/util/35mm_landscape.gif" alt="Photo of Listing #<?=($result['idx'] == 'cms' ? $result['ListingMLSNumber'] : $result['ListingMLS']); ?>">
		</div>
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
	</a>
	<ul class="dataset">
		<?php if(isset($_COMPLIANCE['results']['provider_first']) && $_COMPLIANCE['results']['provider_first']($result)) {
			echo '<li class="provider">';
			\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result);
			echo '</li>';
		} ?>
		<li class="data-mlsid"><strong><?=Lang::write('MLS_NUMBER'); ?>:</strong> <?=($result['idx'] == 'cms' ? $result['ListingMLSNumber'] : $result['ListingMLS']); ?></li>
		<li class="data-address"><?=$result['Address']; ?></li>
		<li class="data-location"><?=$result['AddressCity']; ?>, <?=$result['AddressState']; ?> <?=$result['AddressZipCode']; ?></li>
		<li class="data-price">$<?=Format::number($result['ListingPrice']); ?></li>
		<li class="data-summary">
			<?php

				// Details
				$details = array();
				if ($result['NumberOfBedrooms']		> 0) $details[] = $result['NumberOfBedrooms'] . ' Bed';
				if ($result['NumberOfBathrooms']	> 0) $details[] = Format::fraction($result['NumberOfBathrooms']) . ' Bath';
				if ($result['NumberOfSqFt']			> 0) $details[] = $result['NumberOfSqFt'] . ' <abbr title="Square Feet">SqFt</abbr>';
				if ($result['NumberOfAcres']		> 0) $details[] = (float) $result['NumberOfAcres'] . ' ' . Format::plural($result['NumberOfAcres'], 'Acres', 'Acre');
				echo implode(', ', $details);

			?>
		</li>
		<li><?=$result['ListingType'] . (!empty($_COMPLIANCE['results']['show_status']) ? ' - ' . $result['ListingStatus'] : ''); ?></li>
		<?php if(empty($_COMPLIANCE['results']['provider_first'])){ ?>
			<li class="provider"><?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result); ?></li>
		<?php } ?>

	<?php if ($result['idx'] != 'cms') { ?>
		<a class="action-favorite save" href="#" data-save='<?=json_encode(array(
			'div'		=> '#listing-' . $result['ListingMLS'],
			'mls'		=> $result['ListingMLS'],
			'feed'		=> $result['idx'],
			'remove'	=> 'Remove ' . Locale::spell('Favorite'),
			'add'		=> 'Add as ' . Locale::spell('Favorite')
		)); ?>'
		data-event='favorite' <?php $tracking = (IDX_Compliance::FormatListingTrackable($result)); foreach ($tracking as $service => $listing) { echo "data-listing-" . $service . "='" . Format::htmlspecialchars(json_encode($listing)) . "'"; } ?>>
			<?=(!empty($bookmarked[$result['ListingMLS']])) ? 'Remove ' . Locale::spell('Favorite') : 'Add as ' . Locale::spell('Favorite'); ?>
		</a>
	<?php } ?>
</article>
