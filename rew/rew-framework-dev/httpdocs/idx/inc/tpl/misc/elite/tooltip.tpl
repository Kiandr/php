<div class="uk-thumbnail uk-thumbnail-expand">
	
	<a href="<?= Format::htmlspecialchars($listing_tooltip['url_details']); ?>" title="Click to view details">
		<img src="<?=IDX_Feed::thumbUrl($listing_tooltip['ListingImage'], IDX_Feed::IMAGE_SIZE_SMALL); ?>" alt="Photo of Listing #<?= Format::htmlspecialchars($listing_tooltip['ListingMLS']); ?>">
	</a>
		
  <div class="uk-thumbnail-caption uk-text-left">

        <?php if(!empty($_COMPLIANCE['results']['provider_first']) && $_COMPLIANCE['results']['provider_first']($listing_tooltip)) { ?>
            <div class="provider">
                <?= \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($listing_tooltip); ?>
            </div>
        <?php } ?>
		
		<div class="uk-clearfix">
			<div class="uk-text-large uk-text-uppercase uk-text-bold uk-float-left">$<?=Format::number($listing_tooltip['ListingPrice']); ?></div>
			<div class="uk-float-right"><a class="action-close" href="javascript:void(0);"><i class="uk-icon uk-icon-remove"></i></a></div>
		</div>
		<div class="uk-text-small"><?=Lang::write('MLS_NUMBER'); ?><?= Format::htmlspecialchars($listing_tooltip['ListingMLS']); ?></div>
		
  	<div class="uk-margin-small-top uk-margin-small-bottom">
  		<?=$listing_tooltip['ListingType']; ?><br>
			<?= Format::htmlspecialchars($listing_tooltip['NumberOfBedrooms']); ?> Bed, 
			<?= Format::htmlspecialchars($listing_tooltip['NumberOfBathrooms']); ?> Bath, 
			<?= (!empty($listing_tooltip['NumberOfSqFt']) ? Format::number($listing_tooltip['NumberOfSqFt']) . ' ' . Lang::write('MAP_POPUP_SQFT_TEXT') . ' ' : ''); ?><br>
			<?= Format::htmlspecialchars($listing_tooltip['Address']); ?><br>
			<?= Format::htmlspecialchars($listing_tooltip['AddressCity']); ?>, <?= Format::htmlspecialchars($listing_tooltip['AddressState']); ?>
			
			<?php if (!empty($_COMPLIANCE['results']['show_above_actions'])) { ?>
				<div><?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($listing_tooltip); ?></div>
			<?php } ?>
		</div>
		<div class="uk-margin-small-top">
			<a href="<?= Format::htmlspecialchars($listing_tooltip['url_details']); ?>" class="uk-button uk-button-block">View Details</a>
		</div>
		
		<?php if (empty($_COMPLIANCE['results']['show_above_actions']) && (empty($_COMPLIANCE['results']['provider_first']) || $_COMPLIANCE['results']['provider_first']($listing_tooltip) == false)) { ?>
			<footer class="uk-text-small">
				<?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($listing_tooltip); ?>
				<?=(!empty($_COMPLIANCE['popup']['show_disclaimer'])) ? \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(true) : ''; ?>
			</footer>
		<?php } ?>
	  	
  </div>
</div>
