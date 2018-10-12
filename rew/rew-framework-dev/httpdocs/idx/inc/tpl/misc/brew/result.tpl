<?php
// Compliance Provider First Display Fix
$extra_class = '';
if (isset($_COMPLIANCE['results']['provider_first']) && $_COMPLIANCE['results']['provider_first']($result)) {
	$extra_class = ' provider-first';
}
?>
<article class="listing<?=!empty($bookmarked[$result['ListingMLS']]) ? ' saved' : ''; ?><?=$extra_class?>" id="listing-<?=$result['ListingMLS']; ?>">

	<header>
	    <h4>
	    	<a href="<?=$result['url_details']; ?>" class="info-links">
	    		<?php if (!empty($result['ListingTitle'])) { ?>
	    			<?=$result['ListingTitle']; ?>
			    <?php } else if (strlen($result['ListingRemarks']) > 50) { ?>
			    	<?=htmlspecialchars(Format::truncate(ucwords(strtolower($result['ListingRemarks'])), 50)); ?>
			    <?php } else { ?>
			    	<?php if (!empty($result['NumberOfBedrooms'])) echo Format::number($result['NumberOfBedrooms']) . ' Bedroom'; ?>
			    	<?=$result['ListingType']; ?> Property in <?=ucwords(strtolower($result['AddressCity'])); ?>, <?=$result['AddressState']; ?>
			    <?php } ?>
	    	</a>
	    </h4>
	</header>

    <div class="body">

		<div class="photo">
			<a href="<?=$result['url_details']; ?>" class="info-links"><img data-src="<?=IDX_Feed::thumbUrl($result['ListingImage'], IDX_Feed::IMAGE_SIZE_MEDIUM); ?>" src="/img/util/35mm_landscape.gif" alt="Photo of Listing #<?=($result['idx'] == 'cms' ? $result['ListingMLSNumber'] : $result['ListingMLS']); ?>"></a>

			<?php
			// Compliance Auction Banner Display
			if (is_callable($_COMPLIANCE['is_up_for_auction']) && $_COMPLIANCE['is_up_for_auction']($result)) {
				echo '<span class="flag flag-auction">AUCTION</span>';
			}
			?>

			<?php //Compliance - If EREB/CREB - LECs 2010, 2011 and 2012 highly benefit from fave button here for design ?>
			<?php if (!empty($_COMPLIANCE['results']['fav_first']) && $result['idx'] != 'cms') { ?>
            <a rel="nofollow" href="javascript:void(0);" onclick="$('#listing-<?=$result['ListingMLS']; ?>').Favorite({'feed':'<?=$result['idx']; ?>','mls':'<?=$result['ListingMLS']; ?>'});" class="btn vanilla save" title="Save this Property"><i class="icon"></i></a>
        	<?php } ?>

		</div>

        <?php if(isset($_COMPLIANCE['results']['provider_first']) && $_COMPLIANCE['results']['provider_first']($result)) {
			echo '<div class="provider">';
            \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result);
			echo '</div>';
        } ?>

		<div class="details">

			<h4 class="val price"><?=!empty($result['ListingPrice']) ? '$' . Format::number($result['ListingPrice']) : '&nbsp;'; ?></h4>

			<p class="val location"><?=ucwords(strtolower($result['Address'])); ?></p>

			<?php if (!empty($result['NumberOfBedrooms']) || !empty($result['NumberOfBathrooms']) || !empty($result['NumberOfSqFt'])) { ?>

				<ul class="keyvalset">
					<?php if (!empty($result['NumberOfBedrooms'])) { ?>
						<li class="keyval beds"><span><?=Format::number($result['NumberOfBedrooms']); ?></span>
						<strong title="Bedrooms">Beds</strong></li>
					<?php } ?>
					<?php if (floatval($result['NumberOfBathrooms']) > 0) { ?>
						<li class="keyval baths"><span><?=Format::fraction($result['NumberOfBathrooms']); ?></span>
						<strong title="Bathrooms">Baths</strong></li>
					<?php } ?>
					<?php if (!empty($result['NumberOfSqFt'])) { ?>
						<li class="keyval sqft"><span><?=Format::number($result['NumberOfSqFt']); ?></span>
						<strong title="Square Feet">SqFt</strong></li>
					<?php } ?>
					<?php if (floatval($result['NumberOfAcres']) > 0) { ?>
						<li class="keyval acres"><span><?=Format::number($result['NumberOfAcres'], 2); ?></span>
						<strong>Acres</strong></li>
					<?php } ?>
				</ul>

			<?php } else if (floatval($result['NumberOfAcres']) > 0) { ?>

				<ul class="keyvalset land">
					<li class="keyval acres"><span><?=Format::number($result['NumberOfAcres'], 2); ?></span>
					<strong>Acres</strong></li>
				</ul>

			<?php } else { ?>

				<p class="val">&nbsp;</p>

			<?php } ?>

            <p class="val proptype"><?=$result['ListingType']; ?></p>
			<p class="val adr"><span class="locality"><?=ucwords(strtolower($result['AddressCity'])); ?></span>, <?=$result['AddressState']; ?></p>

			<?php if (!empty($_COMPLIANCE['results']['show_mls'])) { ?>
                <p class="val mls"><?=Lang::write('MLS_NUMBER'); ?><?=($result['idx'] == 'cms' ? $result['ListingMLSNumber'] : $result['ListingMLS']); ?></p>
            <?php } ?>

			<p class="description"><?=htmlspecialchars(Format::truncate(ucwords(strtolower($result['ListingRemarks'])), 100)); ?></p>

			<?php if (!empty($_COMPLIANCE['results']['show_above_actions'])) { ?>
				<div><?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result); ?></div>
			<?php } ?>

	        <?php //Compliance - If NOT EREB/CREB - leave faves where it was ?>
	        <?php if (empty($_COMPLIANCE['results']['fav_first']) && $result['idx'] != 'cms') { ?>
	            <a rel="nofollow" href="javascript:void(0);" onclick="$('#listing-<?=$result['ListingMLS']; ?>').Favorite({'feed':'<?=$result['idx']; ?>','mls':'<?=$result['ListingMLS']; ?>'});" class="btn vanilla save" title="Save this Property"
	            data-event='favorite' <?php $tracking = (IDX_Compliance::FormatListingTrackable($result)); foreach ($tracking as $service => $listing) { echo "data-listing-" . $service . "='" . Format::htmlspecialchars(json_encode($listing)) . "'"; } ?>><i class="icon"></i></a>
	        <?php } ?>

		</div>

		<div class="btnset">
			<a href="<?=$result['url_details']; ?>" class="btn strong">View Listing <i class="icon-chevron-right"></i></a>
		</div>

    </div>

	<?php if (empty($_COMPLIANCE['results']['show_above_actions'])
		&& (!isset($_COMPLIANCE['results']['provider_first']) || $_COMPLIANCE['results']['provider_first']($result) == false)) { ?>
		<footer><?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result); ?></footer>
	<?php } ?>

</article>
