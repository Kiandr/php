<?php

if (!empty($results)) {
	echo '<div class="articleset listings' . (!empty($viewClass) ? ' '. $viewClass : '') . '">';
	foreach ($results as $result) {
		\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->load($result['idx']);

		// Only if not restricted to specific office
		if ((!empty($_COMPLIANCE['featured']['broker']) || !empty($_COMPLIANCE['featured']['office_id'])) && $this->config('mode') === 'featured') {
		    $_COMPLIANCE['results']['show_mls'] = (isset($_COMPLIANCE['featured']['show_mls']))? $_COMPLIANCE['featured']['show_mls'] : $_COMPLIANCE['results']['show_mls'];
		    $_COMPLIANCE['results']['show_icon'] = (isset($_COMPLIANCE['featured']['show_icon']))? $_COMPLIANCE['featured']['show_icon'] : $_COMPLIANCE['results']['show_icon'];
		    $_COMPLIANCE['results']['show_agent'] = (isset($_COMPLIANCE['featured']['show_agent']))? $_COMPLIANCE['featured']['show_agent'] : $_COMPLIANCE['results']['show_agent'];
		    $_COMPLIANCE['results']['show_office'] = (isset($_COMPLIANCE['featured']['show_office']))? $_COMPLIANCE['featured']['show_office'] : $_COMPLIANCE['results']['show_office'];
		    $_COMPLIANCE['results']['lang']['provider'] = (isset($_COMPLIANCE['featured']['lang']['provider']))? $_COMPLIANCE['featured']['lang']['provider'] : $_COMPLIANCE['results']['lang']['provider'];

		    $_REQUEST['snippet'] = false;
		}

?>
<article class="listing featured">
	<div class="photo">
		<a href="<?=$result['url_details']; ?>">
			<img src="/thumbs/200x200/img/blank.gif" data-src="<?=IDX_Feed::thumbUrl($result['ListingImage'], IDX_Feed::IMAGE_SIZE_MEDIUM); ?>" alt="">
		</a>
	</div>
	<div class="badge">
		<strong>
			<?php if($result['ListingPrice'] < $result['ListingPriceOld']) { ?>
				Reduced!
			<?php } else if($result['ListingDOM'] < 7) { ?>
				New
			<?php } else { ?>
				&nbsp;
			<?php } ?>
		</strong>
		<div class="len<?=strlen($result['ListingPrice']); ?>"><i>$</i><?=Format::number($result['ListingPrice']); ?></div>
		<span><?=$result['ListingType']; ?></span>
	</div>

	<?php if (isset($_COMPLIANCE['results']['provider_first']) && $_COMPLIANCE['results']['provider_first']($result)) { ?>
		<div class="provider"><?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result);?></div>
	<?php } ?>
	<div class="body">
		 <div class="data-summary">
			<?php

				// Details
				$details = array();
				if ($result['NumberOfBedrooms']		> 0) $details[] = $result['NumberOfBedrooms'] . ' ' . Format::plural($result['NumberOfBedrooms'], 'Beds', 'Bed');
				if ($result['NumberOfBathrooms']	> 0) $details[] = Format::fraction($result['NumberOfBathrooms']) . ' Bath';
				if ($result['NumberOfSqFt']			> 0) $details[] = Format::number($result['NumberOfSqFt']) . ' Ft&#178;';
				echo implode(', ', $details);
			?>
		 </div>

		 <?php if ($_COMPLIANCE['results']['show_mls']) { ?>
			 <strong class="data-mls"><?=Lang::write('MLS_NUMBER').$result['ListingMLS']; ?></strong>
		 <?php } ?>
		 <strong class="data-location"><?=$result['AddressCity']; ?>, <?=$result['AddressState']; ?></strong>
		 <p><?=Format::truncate($result['ListingRemarks'], 240); ?></p>
	</div>

	<?php if (!isset($_COMPLIANCE['results']['provider_first']) || $_COMPLIANCE['results']['provider_first']($result) == false) {
		\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result);
	} ?>
</article>
<?php

	}

	if (!empty($_COMPLIANCE['results']['show_immediately_below_listings']) && (empty($_COMPLIANCE['featured']['broker']) && empty($_COMPLIANCE['featured']['office_id']))) {
		echo '<div class="show-immediately-below-listings">';
		// Show Disclaimer
		\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(true);
		echo '</div>';
	}
	echo '</div>';
}
