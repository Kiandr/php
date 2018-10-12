<?php

// Listing Not Found
if (empty($listing)) {
	echo '<h1>Listing Not Found</h1>';
	echo '<p class="msg negative">The selected listing could not be found. This is probably because the listing has been sold, removed from the market, or simply moved.</p>';

} else {

	// Listing Details
	$_DETAILS = $idx->getDetails() ? $idx->getDetails() : array();

	// Process Details
	$details = array();
	foreach ($_DETAILS as $data) {
		$fields = array();
		$paragraphs = array();
		foreach ($data['fields'] as $k => $field) {

			// Field Value
			$value = $listing[$field['value']];

			// Format Value
			if (isset($field['format']) && !empty($value)) $value = tpl_format($value, $field['format']);

			// Skip Empty
			if (empty($value)) continue;

			// Length Over 30 Characters
			if (strlen($value) > 30) {
				$paragraphs[] = array('heading' => $field['title'], 'value' => $value);
				continue;
			}

			// Add Data
			$fields[] = array('title' => $field['title'], 'value' => $value);

		}

		// Skip Empty
		if (empty($fields)) continue;

		// Add Details
		$details[] = array('heading' => $data['heading'], 'fields' => $fields);
		$details = array_merge($details, $paragraphs);

	}

	// Load Modules
	$this->container('body')->loadModules();

?>

<div id="listing-details"<?=!empty($bookmarked) ? ' class="saved"' : ''; ?>>

	<?php

		// Compliance Auction Banner Display
		if (is_callable($_COMPLIANCE['is_up_for_auction']) && $_COMPLIANCE['is_up_for_auction']($listing)) {
			echo '<span class="flag flag-auction"><em>AUCTION</em></span>';

		// Price Reduced
		} else if (!is_null($listing['ListingPriceOld']) && $listing['ListingPrice'] < $listing['ListingPriceOld'] && $_COMPLIANCE['flags']['hide_price_reduction'] != true) {
			echo '<span class="flag flag-reduced"><em>REDUCED</em></span>';

		// New Listing
		} else if (!is_null($listing['ListingDOM']) && $listing['ListingDOM'] <= 7) {
			echo '<span class="flag flag-new"><em>NEW</em></span>';
		}

		// Gallery compliance
		$compliance_html = array();
		if (!empty($_COMPLIANCE['gallery']['show_provider'])) {
			$compliance_html[] = '<strong>' . (!empty($_COMPLIANCE['details']['lang']['provider']) ? $_COMPLIANCE['details']['lang']['provider'] : 'Office:') . $listing['ListingOffice'] . '</strong>';
			$compliance_html[] = Lang::write('MLS_NUMBER') . ': ' . $listing['ListingMLS'];
			$compliance_html[] = 'Status: ' . $listing['ListingStatus'];
		}

		if (!empty($_COMPLIANCE['gallery']['show_disclaimer'])) {
			// Disclaimer
			ob_start();
			\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(true);
			$compliance_html[] = ob_get_clean();
		}

		if (!empty($compliance_html)) {
			// Stringify
			$compliance_html = implode('<br>', $compliance_html);
		}

		// Photo Gallery
		$this->container('gallery')->module('gallery', array(
			'compliance'	=> $compliance_html,
			'images'		=> $listing['thumbnails'],
			'links'			=> (!empty($listing['VirtualTour']) ? array(
				array('text' => 'Virtual Tour', 'href' => $listing['VirtualTour'], 'target' => '_blank')
			) : NULL)
		))->display();

	?>

	<div id="listing-body">

		<div class="nav horizontal">
			<ul id="listing-pagination">
				<li><a href="<?=$user->url_back() ?: Settings::getInstance()->SETTINGS['URL_IDX']; ?>"><i class="icon-arrow-left"></i> Back to Results</a></li>
			</ul>
		</div>

		<ul class="dataset">

			<?php if (isset($_COMPLIANCE['details']['provider_first']) && $_COMPLIANCE['details']['provider_first']($listing)) {
				echo '<li class="provider-office">';
				\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing);
				echo '</li>';
			} ?>

			<li class="data-mlsid"><strong><?=Lang::write('MLS_NUMBER'); ?>:</strong> <?=($listing['idx'] == 'cms' ? $listing['ListingMLSNumber'] : $listing['ListingMLS']); ?></li>
			<li class="data-address"><?=(empty($listing['Address']) || ($listing['Address'] == 'N/A') ? '(Undisclosed Address)' : $listing['Address']); ?></li>
			<li class="data-location"><?=$listing['AddressCity']; ?>, <?=$listing['AddressState']; ?> <?=$listing['AddressZipCode']; ?></li>
			<li class="data-price">$<?=Format::number($listing['ListingPrice']); ?></li>
			<li class="data-summary">
				<?php

					// Details
					$basics = array();
					if ($listing['NumberOfBedrooms']	> 0) $basics[] = $listing['NumberOfBedrooms'] . ' ' . Format::plural($listing['NumberOfBedrooms'], 'Beds', 'Bed');
					if ($listing['NumberOfBathrooms']	> 0) $basics[] = Format::fraction($listing['NumberOfBathrooms']) . ' Bath';
					if ($listing['NumberOfSqFt']		> 0) $basics[] = $listing['NumberOfSqFt'] . ' <abbr title="Square Feet">' . Lang::write('IDX_DETAILS_SQFT_TEXT') . '</abbr>';
					if ($listing['NumberOfAcres']		> 0) $basics[] = $listing['NumberOfAcres'] . ' ' . Format::plural($listing['NumberOfAcres'], 'Acres', 'Acre');
					echo implode(', ', $basics);

				?>
			</li>
			<li><?=$listing['ListingType']; ?></li>

			<?php if (empty($_COMPLIANCE['provider']['above_inquire']) && (!isset($_COMPLIANCE['details']['provider_first']) || $_COMPLIANCE['details']['provider_first']($listing) == false)) { ?>
				<?php if (!empty($_COMPLIANCE['details']['show_agent'])) { ?>
					<li class="provider-agent">
						<strong>Agent:</strong>
						<span><?=$listing['ListingAgent']; ?></span>
					</li>
				<?php } ?>
				<?php if (!empty($_COMPLIANCE['details']['show_office'])) { ?>
					<li class="provider-office">
						<strong><?=(!empty($_COMPLIANCE['details']['lang']['provider']) ? $_COMPLIANCE['details']['lang']['provider'] : 'Office:'); ?></strong>
						<span><?=$listing['ListingOffice']; ?></span>
					</li>
				<?php } ?>
				<?php if (!empty($_COMPLIANCE['details']['show_icon'])) { ?>
					<li class="provider-icon">
						<span><?=$_COMPLIANCE['details']['show_icon']; ?></span>
					</li>
				<?php } ?>
				<?php if (!empty($_COMPLIANCE['details']['show_office_phone'])) { ?>
					<li class="provider-office-phone">
						<strong>Office Phone:</strong>
						<span><?=$listing['ListingOfficePhoneNumber']; ?></span>
					</li>
				<?php } ?>
			<?php } ?>
		</ul>

		<p class="remarks"><?=($idx->getLink() == 'cms') ? nl2br(htmlspecialchars($listing['ListingRemarks'])) : htmlspecialchars($listing['ListingRemarks']); ?></p>

		<?php if (!empty($_COMPLIANCE['details']['show_below_remarks'])) { ?>
		<p class="remarks">
			<?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing); ?>
		</p>
		<?php } ?>

		<div class="btnset">
			<a rel="nofollow" class="btn strong popup" href="<?=$listing['url_inquire']; ?>">Ask about this Property</a>
			<a rel="nofollow" class="btn action-share popup" title="Share this Listing" href="<?=$listing['url_sendtofriend']; ?>"><i class="ico-share"></i></a>
			<a rel="nofollow" class="btn action-favorite" title="Add to <?=Locale::spell('Favorites'); ?>" href="#"><i class="ico-fav"></i></a>
			<?php if (!empty(Settings::getInstance()->MODULES['REW_RT']) && !empty($listing['AddressState'])) { ?>
				<?php if ($rt_link = \RealtyTrac\Integration::get_rt_from_mls($idx->getName(), $listing['ListingMLS'], $listing['AddressState'])) { ?>
					<br><a rel="nofollow" class="btn strong" href="<?=$rt_link; ?>">View Public Record</a>
				<?php } ?>
			<?php } ?>
			<!--<a rel="nofollow" class="btn action-print" href="<?=$listing['url_brochure']; ?>" target="_blank"></a>-->
			<?php if (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO']) { ?>
				<div class="hidden-phone">
					<a rel="nofollow" class="btn strong popup" href="<?=$listing['url_phone']; ?>">Keep this listing at your fingertips</a>
				</div>
			<?php } ?>
		</div>

		<?php if (!empty($_COMPLIANCE['details']['extra_remarks'])) { ?>
			<p class="remarks"><?=$_COMPLIANCE['details']['extra_remarks']; ?></p>
		<?php } ?>

		<?php

			// Listing Details
			if (!empty($details)) {
				foreach ($details as $info) {
					echo '<div class="dataset">';
					echo '<h4>' . $info['heading'] . '</h4>';
					if (!empty($info['fields'])) {
						echo '<ul>';
						foreach ($info['fields'] as $field) {
							echo '<li>';
							echo '<strong>' . $field['title'] . '</strong>';
							echo '<span>' . $field['value'] . '</span>';
							echo '</li>';
						}
					 	echo '</ul>';
					} elseif(!empty($info['value'])) {
						echo '<p>' . $info['value'] . '</p>';
					}
					echo '</div>';
				}
			}

		?>

		<a href="javascript:window.print();">Print this Page</a>

        <?php if (!empty($_COMPLIANCE['details']['logos']) && is_array($_COMPLIANCE['details']['logos'])) { ?>
        <div class="details-logos">
            <?php foreach ($_COMPLIANCE['details']['logos'] as $logo) { ?>
            <img src="<?=$logo; ?>">
            <?php } ?>
        </div>
        <?php } ?>

	</div>

</div>

<?php

// Wrap Extra
echo '<div id="extraDetails">';

// IDX Mapping Enabled
if (!empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) {

	// Require Listing Co-ordinates
	if (!empty($listing['Latitude']) && !empty($listing['Longitude'])) {

 ?>
<section>
	<hgroup>
		<h3>Property Location</h3>
		<div class="nav horizontal views">
			<ul class="hidden">
				<?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_DIRECTIONS'])) { ?>
					<li class="current"><a href="#map">Directions</a></li>
				<?php } else { ?>
					<li class="current"><a href="#map">Listing Map</a></li>
				<?php } ?>
				<?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_STREETVIEW'])) { ?>
					<li class="hidden"><a href="#streetview">Streetview</a></li>
				<?php } ?>
				<?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_BIRDSEYE'])) { ?>
					<li><a href="#birdseye">Bird's Eye</a></li>
				<?php } ?>
				<?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_ONBOARD'])) { ?>
					<li><a href="#local">Amenities &amp; Schools</a></li>
				<?php } ?>
			</ul>
		</div>
	</hgroup>
	<div id="map-canvas"></div>
	<?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_DIRECTIONS'])) { ?>
		<div id="map-directions" class="msg">
			<form>
				<strong>Get directions to this property:</strong>
				<input name="from" placeholder="From Address&hellip;" required>
				<input type="hidden" name="to" value="<?=htmlspecialchars(!empty($listing['Latitude']) && !empty($listing['Longitude']) ? $listing['Latitude'].','.$listing['Longitude'] : $listing['Address'] . ', ' . $listing['AddressCity'] . ' ' . $listing['AddressZipCode']); ?>">
				<div class="btnset">
					<button class="strong" type="submit">Get Directions</button>
				</div>
			</form>
			<div id="directions"></div>
		</div>
	<?php } ?>
	<div id="map-streetview" class="hidden"></div>
	<div id="map-birdseye" class="hidden"></div>
</section>
<?php

		// Tooltip
		ob_start();
		$listing_tooltip = $listing;
		include $page->locateTemplate('idx', 'misc', 'tooltip');
		$tooltip = str_replace(array("\r\n", "\n", "\t"), "", ob_get_clean());

		// Map Options
		$mapOptions = array(
			'streetview' => !empty(Settings::getInstance()->MODULES['REW_IDX_STREETVIEW']),
			'center' => array('lat' => $listing['Latitude'], 'lng' => $listing['Longitude']),
			'manager' => array(
				'bounds' => false,
				'markers' => array(array(
					'tooltip' => $tooltip,
					'lat' => $listing['Latitude'],
					'lng' => $listing['Longitude']
				))
			)
		);

		// Dynamic JS vars
		$page->addJavascript('
			var URL_LOCAL = \'' . $listing['url_onboard'] . '?popup\';
			var MAP_LATITUDE = ' . floatval($listing['Latitude']) . ';
			var MAP_LONGITUDE = ' . floatval($listing['Longitude']) . ';
			var MAP_OPTIONS = ' . json_encode($mapOptions) . ';
		', 'dynamic', false);

		// Require details page javascript
		$page->addJavascript('js/idx/details.js', 'page');

	}

}

if (empty($_COMPLIANCE['details']['show_below_details'])) {

	// Feed-specific compliance
	if (!empty($_COMPLIANCE['provider']['above_inquire'])) {
		// Show MLS Office / Agent
		\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing);
	}
	if (!empty($_COMPLIANCE['details']['above_inquire'])) {
		// Show Disclaimer
		\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer();
	}

	// Include Contact Form
	include $page->locateTemplate('idx', 'misc', 'allure-inquire');

}

// Price Change History
include $page->locateTemplate('idx', 'misc', 'history');

// Close Wrap
echo '</div>';

if (!empty($_COMPLIANCE['details']['show_below_details'])) {
	echo '<div class="details-foot">';
	// Show MLS Office / Agent
	if (empty($_COMPLIANCE['details']['show_below_remarks'])) {
		\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing);
	}

	// Show Disclaimer
	\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer();

	// Include Contact Form
	include $page->locateTemplate('idx', 'misc', 'allure-inquire');
	echo '</div>';
}

// Start JavaScript
ob_start();

?>
(function () {

	// Save to Favorites
	$('a.action-favorite').on('click', function () {
		$('#listing-details').Favorite({
			mls: '<?=$listing['ListingMLS']; ?>',
			feed: '<?=!empty($listing['idx']) ? $listing['idx'] : Settings::getInstance()->IDX_FEED; ?>'
		});
		return false;
	});

	// Pagination
	IDX.Paginate({
		mls: <?=json_encode($listing['ListingMLS']) ; ?>,
		feed: <?=json_encode($listing['idx']) ; ?>,
		done: function (data) {
			var $wrap = $('#listing-pagination');
			if (data.prev){
				$('<li>\
					<a href="' + data.prev + '">\
						<i class="icon-chevron-left"></i> Prev\
					</a>\
				</li>').appendTo($wrap);
			}
			if (data.next) {
				$('<li>\
					<a href="' + data.next + '">\
						Next <i class="icon-chevron-right"></i>\
					</a>\
				</li>').appendTo($wrap);
			}
		}
	});

})();
<?php

	// Add dynamic javascript to page (don't minify)
	$page->addJavascript(ob_get_clean(), 'dynamic', false);

}
