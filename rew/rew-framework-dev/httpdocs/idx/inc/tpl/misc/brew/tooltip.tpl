<?php global $_COMPLIANCE;?>
<div class="popover">
	<header class="title">
		<?php if(!empty($_COMPLIANCE['results']['provider_first']) && $_COMPLIANCE['results']['provider_first']($listing_tooltip)) {
			echo '<div class="provider">';
			\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($listing_tooltip);
			echo '</div>';
		} ?>
		<a href="<?=$listing_tooltip['url_details']; ?>" target="_parent">
			<strong><?=$listing_tooltip['Address']; ?></strong> <small><?=Lang::write('MLS_NUMBER'); ?><?=$listing_tooltip['ListingMLS']; ?></small>
		</a>
		<a class="action-close hidden" href="javascript:void(0);">&times;</a>
	</header>
	<div class="body">
		<div class="photo pleft">
			<a href="<?=$listing_tooltip['url_details']; ?>" target="_parent">
				<img src="<?=IDX_Feed::thumbUrl($listing_tooltip['ListingImage'], IDX_Feed::IMAGE_SIZE_SMALL); ?>" alt="Photo of Listing #<?=$listing_tooltip['ListingMLS']; ?>" width="120" border="0" />
			</a>
		</div>
		<div class="summary">
	 		<h4 class="price">$<?=Format::number($listing_tooltip['ListingPrice']); ?></h4>
			<div class="basics">
				<?=$listing_tooltip['NumberOfBedrooms']; ?> Bed, <?=$listing_tooltip['NumberOfBathrooms']; ?> Bath,
				<?=(!empty($listing_tooltip['NumberOfSqFt']) ? Format::number($listing_tooltip['NumberOfSqFt']) . ' ' . Lang::write('MAP_POPUP_SQFT_TEXT') . ' ' : ''); ?>
				<em><?=$listing_tooltip['ListingType']; ?></em>
			</div>
			<div class="location"><?=$listing_tooltip['Address']; ?>, <?=$listing_tooltip['AddressCity']; ?></div>
			<?php if (!empty($_COMPLIANCE['results']['show_above_actions'])) { ?>
				<div><?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($listing_tooltip); ?></div>
			<?php } ?>
		</div>
		<div class="btnset mini">
			<a href="<?=$listing_tooltip['url_details']; ?>" class="btn" target="_parent">View<span class="extra"> Details</span></a>
		</div>
	</div>
	<?php if (empty($_COMPLIANCE['results']['show_above_actions']) && (empty($_COMPLIANCE['results']['provider_first']) || $_COMPLIANCE['results']['provider_first']($listing_tooltip) == false)) { ?>
		<footer>
		<?php
		if(!($_COMPLIANCE['hide_office_map'])) { ?>
			<?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($listing_tooltip); ?>
			<?=(!empty($_COMPLIANCE['popup']['show_disclaimer'])) ? \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(true) : ''; ?>
		<?php } ?>
		</footer>
	<?php } ?>
	<div class="tail"></div>
</div>
