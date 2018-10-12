<?php

// Listing Not Found
if (empty($listing)) {
	echo '<h1>Listing Not Found</h1>';
	echo '<p class="msg negative">The selected listing could not be found. This is probably because the listing has been sold, removed from the market, or simply moved.</p>';

} else {

	// Basic Details
	$basics = array(
		// Price
		'price' => array(
			'title' => 'Price',
			'show'  => ($listing['ListingPrice'] > 0),
			'data'  => '$' . Format::number($listing['ListingPrice'])
		),
		// # Of Bedrooms
		'bedrooms' => array(
			'title' => 'Beds',
			'show'  => ($listing['NumberOfBedrooms'] > 0),
			'data'  => Format::number(intval($listing['NumberOfBedrooms']))
		),
		// # Of Bathrooms
		'bathrooms' => array(
			'title' => 'Baths',
			'show'  => ($listing['NumberOfBathrooms'] > 0),
			'data'  => Format::fraction($listing['NumberOfBathrooms'])
		),
		// # Of Sq. Ft.
		'sqft' => array(
			'title' => Lang::write('IDX_DETAILS_SQFT_TEXT'),
			'show'  => ($listing['NumberOfSqFt'] > 0),
			'data'  => Format::number($listing['NumberOfSqFt'])
		),
		// # Of Acres
		'acres' => array(
			'title' => 'Acres',
			'show'  => ($listing['NumberOfAcres'] > 0),
			'data'  => Format::number(floatval($listing['NumberOfAcres']), 2)
		),
		// Year Built
		'year'  => array(
			'title' => 'Built',
			'show'  => !empty($listing['YearBuilt']),
			'data'  => $listing['YearBuilt']
		)
	);

	// Listing Details
	$_DETAILS = $idx->getDetails() ? $idx->getDetails() : array();

	// MLS Compliance (Agent / Office)
	if (empty($_COMPLIANCE['details']['show_below_remarks']) && (!isset($_COMPLIANCE['details']['provider_first']) || $_COMPLIANCE['details']['provider_first']($listing) == false)){
		if (is_callable($_COMPLIANCE['details']['extra'])) {
			if ($details_extra = $_COMPLIANCE['details']['extra']($idx, $db_idx, $listing, $_COMPLIANCE)) {
				foreach ($details_extra as $extra) {
					if (!empty($_COMPLIANCE['details']['details_provider_first'])) {
						array_unshift($_DETAILS, $extra);
					} else {
						array_push($_DETAILS, $extra);
					}
				}
			}
		} else if (empty($_COMPLIANCE['provider']['above_inquire'])) {
			$provider_info = array('heading' => (!empty($_COMPLIANCE['details']['lang']['listing_details']) ? $_COMPLIANCE['details']['lang']['listing_details'] : 'Listing Details'), 'fields' => array(
				!empty($_COMPLIANCE['details']['show_agent']) ? array('title' => 'Listing Agent', 'value' => 'ListingAgent') : null,
				!empty($_COMPLIANCE['details']['show_office']) ? array('title' => (!empty($_COMPLIANCE['details']['lang']['provider']) ? $_COMPLIANCE['details']['lang']['provider'] : 'Listing Office'), 'value' => 'ListingOffice') : null,
				!empty($_COMPLIANCE['details']['show_office_phone']) ? array('title' => 'Office Phone', 'value' => 'ListingOfficePhoneNumber') : null,
				!empty($_COMPLIANCE['details']['show_icon']) ? array('block' => $_COMPLIANCE['details']['show_icon']) : false
			));
			if (!empty($_COMPLIANCE['details']['details_provider_first'])) {
				array_unshift($_DETAILS, $provider_info);
			} else {
				array_push($_DETAILS, $provider_info);
			}
		}
	}

	// Process Details
	$details = array();
	foreach ($_DETAILS as $data) {
		$fields = array();
		foreach ($data['fields'] as $k => $field) {

			if (!empty($field['block'])) {
				$value = $field['block'];
			} else {
				// Field Value
				$value = $listing[$field['value']];
			}

			// Format Value
			if (isset($field['format']) && !empty($value)) $value = tpl_format($value, $field['format']);

			// Skip Empty
			if (empty($value)) continue;

			// Add Data
			$fields[] = array('title' => $field['title'], 'value' => $value, 'attributes' => $field['attributes']);

		}

		// Skip Empty
		if (empty($fields)) continue;

		// Add Details
		$details[] = array('heading' => $data['heading'], 'fields' => $fields);

	}

?>
	<div class="listing detail<?=!empty($bookmarked) ? ' saved' : ''; ?>">

		<?=(!empty($listing['ListingTitle']) ? '<h1>' . $listing['ListingTitle'] . '</h1>' : '') ; ?>

		<header>

			<?php

				// Details Tabset
				include $page->locateTemplate('idx', 'misc', 'details');

				// Display HTML
				if (!empty($basics)) {
					echo '<ul class="keyvalset">';
					foreach ($basics as $id => $data) {
						if (empty($data['show'])) continue;
						echo '<li class="keyval ' . $id . '">';
						echo '<strong>' . $data['title'] . '</strong>';
						echo '<span>' . $data['data'] . '</span>';
						echo '</li>';
					}
					echo '</ul>';
				}

			?>

		</header>

		<?php include $page->locateTemplate('idx', 'misc', 'gallery'); ?>

		<div class="body">

			<h1 class="title"><?=empty($listing['Address']) || ($listing['Address'] == 'N/A') ? '(Undisclosed Address)' : $listing['Address']; ?>, <?=$listing['AddressCity']; ?></h1>
			<p class="remarks"><?=($idx->getLink() == 'cms') ? nl2br(htmlspecialchars($listing['ListingRemarks'])) : htmlspecialchars($listing['ListingRemarks']); ?></p>

			<?php if (!empty($_COMPLIANCE['details']['show_below_remarks'])) : ?>
				<p class="remarks">
					<?php if (!empty($_COMPLIANCE['details']['show_agent']) || !empty($_COMPLIANCE['details']['show_office'])) : ?>
						<?php if (!empty($_COMPLIANCE['details']['show_agent']) && !empty($listing['ListingAgent'])) : ?>
							<span class="agent">Listing Agent: <?=$listing['ListingAgent']; ?></span><br/>
						<?php endif; ?>
						<?php if (!empty($_COMPLIANCE['details']['show_office']) && !empty($listing['ListingOffice'])) : ?>
							<span class="office">
							<?=(isset($_COMPLIANCE['details']['lang']['provider']) ? $_COMPLIANCE['details']['lang']['provider'] : 'Listing Office: ')?>
							<?=$listing['ListingOffice']; ?>
							</span>
							<?php if (!empty($_COMPLIANCE['details']['show_icon'])) : ?>
								<span class="icon"><?=$_COMPLIANCE['details']['show_icon']; ?></span>
							<?php endif; ?>
							<br/>
						<?php endif; ?>
						<?php if (!empty($_COMPLIANCE['details']['show_office_phone']) && !empty($listing['ListingOfficePhoneNumber'])) : ?>
							<span class="office">Office Phone #: <?=$listing['ListingOfficePhoneNumber']; ?></span>
						<?php endif; ?>
					<?php endif; ?>
				</p>
			<?php endif; ?>

			<div class="btnset grid_12">
				<a rel="nofollow" class="btn x6 strong popup" href="<?=$listing['url_inquire']; ?>">Inquire!</a>
				<a rel="nofollow" class="btn x6 last popup" href="<?=$listing['url_inquire']; ?>?inquire_type=Property+Showing">Request a Showing</a>
				<a rel="nofollow" class="btn x6 save" href="javascript:void(0);" onclick="$('.listing.detail').Favorite({
					feed : '<?=!empty($listing['idx']) ? $listing['idx'] : Settings::getInstance()->IDX_FEED; ?>',
					mls : '<?=$listing['ListingMLS']; ?>',
					onComplete : function (data) {
						var $anchor = $('a.save').find('.text');
						if (data.added) $anchor.text('Remove <?=Locale::spell('Favorite'); ?>');
						if (data.removed) $anchor.text('Add to <?=Locale::spell('Favorites'); ?>');
					}
				});"><span class="ico"></span> <span class="text"><?=(!empty($bookmarked)) ? 'Remove ' . Locale::spell('Favorite') : 'Add to ' . Locale::spell('Favorites'); ?></span></a>
				<a rel="nofollow" class="btn x6 last" href="<?=$listing['url_brochure']; ?>" target="_blank">Print this Listing</a>
				<?php if (!empty(Settings::getInstance()->MODULES['REW_RT']) && !empty($listing['AddressState'])) { ?>
					<?php if ($rt_link = \RealtyTrac\Integration::get_rt_from_mls($idx->getName(), $listing['ListingMLS'], $listing['AddressState'])) { ?>
						<a rel="nofollow" class="btn x12" href="<?=$rt_link; ?>">View Public Record</a>
					<?php } ?>
				<?php } ?>
			</div>

			<?php if (!empty($_COMPLIANCE['details']['extra_remarks'])) { ?>
				<p class="remarks"><?=$_COMPLIANCE['details']['extra_remarks']; ?></p>
			<?php } ?>

		</div>

		<?php include $page->locateTemplate('idx', 'misc', 'nextsteps'); ?>

		<div class="details-extended">
			<?php

				// Display HTML
				if (!empty($details)) {
					// Split Odd and Even
					$even = $odd = array();
					for ($i = 0, $l = count($details); $i < $l;) {
						$even[] = $details[$i++];
						$odd[] = $details[$i++];
					}
					// Display Lists
					foreach (array(array_filter($even), array_filter($odd)) as $details) {
						echo '<div  class="col">';
						foreach ($details as $data) {
							echo '<div class="keyvalset">';
							echo '<h3>' . $data['heading'] . '</h3>';
							echo '<ul>';
							foreach ($data['fields'] as $field) {
								echo '<li class="keyval">';
								echo '<strong>' . $field['title'] . '</strong>';
								echo '<span' . (!empty($field['attributes']) ? ' ' . $field['attributes'] : '') . '>' . $field['value'] . '</span>';
								echo '</li>';
							}
							echo '</ul>';
							echo '</div>';
						}
						echo '</div>';
					}
				}

			?>

            <?php if (!empty($_COMPLIANCE['details']['logos']) && is_array($_COMPLIANCE['details']['logos'])) { ?>
            <div class="details-logos">
                <?php foreach ($_COMPLIANCE['details']['logos'] as $logo) { ?>
                <img src="<?=$logo; ?>">
                <?php } ?>
            </div>
            <?php } ?>

			<?php
			// Feed-specific compliance
			if (!empty($_COMPLIANCE['details']['show_below_details'])) {
				echo '<div class="details-foot">';
				if (empty($_COMPLIANCE['details']['show_below_remarks']) && !empty($_COMPLIANCE['provider']['above_inquire'])) {
					// Show MLS Office / Agent
					\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing);
				}

				// Show Disclaimer
				\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer();
				echo '</div>';
			}
			?>
		</div>

		<?php include $page->locateTemplate('idx', 'misc', 'history'); ?>

	</div>

<?php

	// Feed-specific compliance
	if (empty($_COMPLIANCE['details']['show_below_details'])) {
		if (!empty($_COMPLIANCE['provider']['above_inquire'])) {
			// Show MLS Office / Agent
			\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing);
		}
		if (!empty($_COMPLIANCE['details']['above_inquire'])) {
			// Show Disclaimer
			\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer();
		}
	}

	// Include Contact Form
	include $page->locateTemplate('idx', 'misc', 'allure-inquire');

}
