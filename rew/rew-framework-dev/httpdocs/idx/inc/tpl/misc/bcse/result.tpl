<?php $skin = ($this instanceof Module) ? $this->getContainer()->getPage()->getSkin() : $this->getSkin(); ?>
<article class="media listing has-actions <?=!empty($bookmarked[$result['ListingMLS']]) ? ' saved' : ''; ?>" id="listing-<?=$result['ListingMLS']; ?>">
	<div class="mediaImg">
		<a href="<?=$result['url_details']; ?>" class="info-links" target="_parent">
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
                <?php if(!empty($_COMPLIANCE['results']['provider_first']) && $_COMPLIANCE['results']['provider_first']($result)) { ?>
                    <?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result); ?>
                <?php } ?>
		<hgroup>
			<h3>
				<?=Format::htmlspecialchars(implode(', ', array_filter(array($result['Address'], $result['AddressCity'], $result['AddressState'])))); ?>
				<?=!empty($result['ListingPrice']) ? ' - <span class="colored">$' . Format::number($result['ListingPrice']) . '</span>' : ''; ?>
			</h3>
		</hgroup>
		<div class="mediaBodyRight">
			<div class="property-type">
				<?=Format::htmlspecialchars(implode(', ', array_filter(array(
					$result['ListingType'],
					$result['ListingSubType'],
					(!empty($_COMPLIANCE['results']['show_status']) ? $result['ListingStatus'] : false)
				)))); ?>
			</div>
			<ul class="mediaBodyStats">
				<?php if (!empty($result['NumberOfBedrooms'])) { ?>
					<li><b><?=Format::number($result['NumberOfBedrooms']); ?></b> <?=Format::plural($result['NumberOfBedrooms'], 'Beds', 'Bed'); ?></li>
				<?php } ?>
				<?php if (floatval($result['NumberOfBathrooms']) > 0) { ?>
					<li><b><?=Format::fraction($result['NumberOfBathrooms']); ?></b> <?=Format::plural($result['NumberOfBedrooms'], 'Baths', 'Bath'); ?></li>
				<?php } ?>
				<?php if (!empty($result['NumberOfSqFt'])) { ?>
					<li><b><?=(intval($result['NumberOfSqFt']) > 9999 ?  Format::shortNumber($result['NumberOfSqFt']) : Format::number($result['NumberOfSqFt'])); ?></b> SQFT</li>
				<?php } ?>
				<?php if (floatval($result['ListingDOM']) > 0) { ?>
					<li><b><?=Format::number($result['ListingDOM']); ?></b> DOM</li>
				<?php } ?>
			</ul>
			<span class="actions">
				<a class="buttonstyle colored-bg" href="<?=$result['url_details']; ?>" title="View more information about <?=Format::htmlspecialchars($result['Address']); ?>." target="_parent">View details</a>
				<a href="<?=$result['url_details']; ?>" target="_parent"><i class="icon-camera">More Photos</i></a>
				<?php if ($result['idx'] != 'cms') { ?>
					<a class="action-save"  data-save='<?=json_encode(array(
						'div'		=> '#listing-' . $result['ListingMLS'],
						'mls'		=> $result['ListingMLS'],
						'feed'		=> $result['idx'],
						'remove'	=> 'Remove',
						'add'		=> 'Save'
					)); ?>'
						data-event='favorite' <?php $tracking = (IDX_Compliance::FormatListingTrackable($result)); foreach ($tracking as $service => $listing) { echo "data-listing-" . $service . "='" . Format::htmlspecialchars(json_encode($listing)) . "'"; } ?>>
						<?=(!empty($bookmarked[$result['ListingMLS']]) ? '<i class="icon-star"> <span>Remove</span></i>' : '<i class="icon-star-empty"> <span>Save</span></i>'); ?>
					</a>
				<?php } ?>
			</span>
		</div>
		<div class="mediaBodyLeft">
			<p class="mediaRemarks"><?=htmlspecialchars(Format::truncate(ucwords(strtolower($result['ListingRemarks'])), 250)); ?></p>
			<span class="mediaProvider">
				<?php if (isset($result['ListingFeed'])) \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->load($result['ListingFeed']); ?>
                <?php if(empty($_COMPLIANCE['results']['provider_first']) || $_COMPLIANCE['results']['provider_first']($result) === false) { ?>
                    <?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result); ?>
                <?php } ?>
				<?=(!empty($_COMPLIANCE['results']['show_mls']) ? '<p>' . Lang::write('MLS_NUMBER') . ($result['idx'] == 'cms' ? $result['ListingMLSNumber'] : $result['ListingMLS']) . '</p>' : ''); ?>
			</span>
		</div>
	</div>
</article>
