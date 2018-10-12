<div class="popover">
    <div class="-text-right">
        <a class="action-close" tabindex="0">
            <svg id="icon--close" viewBox="0 0 11.1 11.1" width="12" height="12">
                <polygon points="11.1 1.41 9.68 0 5.55 4.13 1.41 0 0 1.41 4.13 5.55 0 9.68 1.41 11.1 5.55 6.96 9.68 11.1 11.1 9.68 6.96 5.55 11.1 1.41"/>
            </svg>
        </a>
    </div>
	<div class="article">
		<a class="article__photo hero hero--landscape" href="<?=$listing_tooltip['url_details']; ?>" target="_parent">
			<div class="hero__bg">
				<img class="hero__bg-content" src="<?=IDX_Feed::thumbUrl($listing_tooltip['ListingImage'], IDX_Feed::IMAGE_SIZE_MEDIUM); ?>" alt="Photo of Listing #<?=$listing_tooltip['ListingMLS']; ?>" />
			</div>
		</a>

		<div class="article__body -pad-horizontal-sm -pad-bottom">
            <?php if(!empty($_COMPLIANCE['results']['provider_first']) && $_COMPLIANCE['results']['provider_first']($listing_tooltip)) {
                echo '<div class="provider">';
                \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($listing_tooltip);
                echo '</div>';
            } ?>
			<h4 class="-mar-0">
				$<?=Format::number($listing_tooltip['ListingPrice']); ?>
				<small class="-text-xs -mar-left-xs">
				<?php
					$details = array();
					if($listing_tooltip['NumberOfBedrooms'] > 0) $details[] = $listing_tooltip['NumberOfBedrooms'] + 0 . ' Beds';
					if($listing_tooltip['NumberOfBathrooms'] > 0) $details[] = $listing_tooltip['NumberOfBathrooms'] + 0 . ' Baths';
					if($listing_tooltip['NumberOfSqFt'] > 0) $details[] = $listing_tooltip['NumberOfSqFt'] + 0 . ' Sqft';
					
					echo implode (', ', $details);
				
				?>
				</small>
			</h4>

			<div class="-text-xs"><?=$listing_tooltip['Address']; ?>, <?=$listing_tooltip['AddressCity']; ?></div>
			<?php if (!empty($_COMPLIANCE['results']['show_above_actions'])) { ?>
				<div><?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($listing_tooltip); ?></div>
			<?php } ?>

			<a href="<?=$listing_tooltip['url_details']; ?>"target="_parent"></a>

		</div>

	<?php if (empty($_COMPLIANCE['results']['show_above_actions']) && (!isset($_COMPLIANCE['results']['provider_first']) || $_COMPLIANCE['results']['provider_first']($listing_tooltip) == false)) { ?>
		<footer>
			<?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($listing_tooltip); ?>
			<?=(!empty($_COMPLIANCE['popup']['show_disclaimer'])) ? \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(true) : ''; ?>
		</footer>
	<?php } ?>
	<div class="tail"></div>

</div>