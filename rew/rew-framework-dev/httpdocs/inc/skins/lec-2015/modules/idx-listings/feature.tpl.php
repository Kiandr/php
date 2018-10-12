<?php

// Featured listing CTA
if (!empty($results)) {
	echo '<div class="col width-2/5">';
	foreach ($results as $result) {

		// Property details
		$details = implode(', ', array_filter(array(
			!empty($result['NumberOfBedrooms']) ? Format::number($result['NumberOfBedrooms']) . ' Beds' : NULL,
			floatval($result['NumberOfBathrooms']) > 0 ? Format::fraction($result['NumberOfBathrooms']) . ' Baths' : NULL,
			!empty($result['NumberOfSqFt']) ? $result['NumberOfSqFt'] . ' Sqft' : NULL
		)));

?>
<a href="<?=$result['url_details']; ?>">
	<div class="photo photo-bordered text-center">
		<img data-src="<?=IDX_Feed::thumbUrl($result['ListingImage'], IDX_Feed::IMAGE_SIZE_MEDIUM); ?>" src="/img/util/35mm_landscape.gif" alt="">
		<div class="body">
			<h4><?=$result['Address']; ?></h4>
			<div class="tagset">
				<?=$result['ListingType']; ?>
				<?=!empty($details) ? ' with ' . $details : ''; ?>
				<br>in <?=implode(', ', array($result['AddressCity'], $result['AddressState'])); ?>
			</div>
		</div>
	</div>
<?php
	if (!empty($_COMPLIANCE['results']['show_mls'])) {
		echo '<div class="navListingsCompliance">' . Lang::write('MLS_NUMBER')
			. $result['ListingMLS'] . '</div><br>';
	}
	if (empty($_COMPLIANCE['featured']['broker']) && empty($_COMPLIANCE['featured']['office_id'])) {
		echo '<div class="navListingsCompliance">';
		\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result);
		echo '</div>';
	}
?>
</a>
<?php
	}
	if (!empty($results) && (empty($_COMPLIANCE['featured']['broker'])
			&& empty($_COMPLIANCE['featured']['office_id']))) {
		\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(true);
	}
	echo '</div>';
}
