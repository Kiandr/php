<?php

// Listing Not Found
if (empty($listing)) {
	echo '<h1>Listing Not Found</h1>';
	echo '<p class="msg negative">The selected listing could not be found. This is probably because the listing has been sold, removed from the market, or simply moved.</p>';

} else {

	// Add data to $listing so we can access it in page.tpl
	$listing['saved'] = !empty($bookmarked);

	// Listing details
	$_DETAILS = $idx->getDetails() ? $idx->getDetails() : array();

	// Compliance requirement
	$show_agent = $_COMPLIANCE['details']['show_agent'];
	$show_office = $_COMPLIANCE['details']['show_office'];
	$show_office_phone = $_COMPLIANCE['details']['show_office_phone'];
	$show_icon = $_COMPLIANCE['details']['show_icon'];
	if ($show_agent || $show_office || $show_office_phone || $show_icon) {
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
		} else {
			$provider_info = array(
				'heading' => $_COMPLIANCE['details']['lang']['listing_details'] ?: 'Listing Details',
				'fields' => array(
					$show_agent ? array('title' => 'Agent', 'value' => 'ListingAgent') : false,
					$show_office ? array(
					    'title' => $_COMPLIANCE['details']['lang']['provider'] ?: 'Office',
					    'value' => 'ListingOffice',
					) : false,
					$show_office_phone ? array('title' => 'Office #', 'value' => 'ListingOfficePhoneNumber') : false,
					$show_icon ? array('block' => $_COMPLIANCE['details']['show_icon']) : false
				)
			);
			if (!empty($_COMPLIANCE['details']['details_provider_first'])) {
				array_unshift($_DETAILS, $provider_info);
			} else {
				array_push($_DETAILS, $provider_info);
			}
		}
	}

	$details = array();
	foreach ($_DETAILS as $data) {
		$fields = array();
		$paragraphs = array();
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

			// Length Over 30 Characters
			if (strlen($value) > 30) {
				$paragraphs[] = array('heading' => $field['title'], 'value' => $value);
				continue;
			}

			// Add Data
			$fields[] = array('title' => $field['title'], 'value' => $value);

		}

		// Skip Empty
		if (empty($fields) && empty($paragraphs)) continue;

		// Add Details
		$details[] = array('heading' => $data['heading'], 'fields' => $fields);
		$details = array_merge($details, $paragraphs);
	}

	// Store listing data in page
	$page->info('listing', $listing);

?>
<div id="listing-details"<?=!empty($bookmarked) ? ' class="saved"' : ''; ?>>

	<div id="listing-gallery">
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

			// Photo Gallery
			$this->container('gallery')->module('gallery', array(
				'enlarge'	=> false,
				'images'	=> $listing['thumbnails'],
				'links'		=> (!empty($listing['VirtualTour']) ? array(
					array('text' => 'Virtual Tour', 'href' => $listing['VirtualTour'], 'target' => '_blank')
				) : NULL)
			))->display();

		?>
	</div>

	<div id="listing-body">

		<h1><?=(empty($listing['Address']) || ($listing['Address'] == 'N/A') ? '(Undisclosed Address)' : $listing['Address']); ?></h1>

		<p class="remarks"><?=($idx->getLink() == 'cms') ? nl2br(htmlspecialchars($listing['ListingRemarks'])) : htmlspecialchars($listing['ListingRemarks']); ?></p>

		<?php if (!empty($_COMPLIANCE['details']['show_below_remarks'])) { ?>
		<p class="remarks">
			<?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing); ?>
		</p>
		<?php } ?>

		<div class="btnset actions">
			<a rel="nofollow" class="buttonstyle popup mini" href="<?=$listing['url_inquire']; ?>" style="margin-right: 5px;">Request More Info</a>
			<?php if (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO']) { ?>
				<a rel="nofollow" class="buttonstyle popup mini hidden-phone" href="<?=$listing['url_phone']; ?>">Send to Mobile Device</a>
			<?php } ?>
		</div>

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

        if (!empty($_COMPLIANCE['details']['logos']) && is_array($_COMPLIANCE['details']['logos'])) { ?>
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

	// Include Contact Form
	include $page->locateTemplate('idx', 'misc', 'allure-inquire');

	// Price Change History
	include $page->locateTemplate('idx', 'misc', 'history');

	// Close Wrap
	echo '</div>';

	// Listing pagination javascript
	$page->addJavascript('IDX.Paginate({
		mls: ' . json_encode($listing['ListingMLS']) . ',
		feed: ' . json_encode($listing['idx']) . ',
		done: function (data) {
			var $wrap = $(\'#sticky-details .top-menu h4\');
			if (data.prev){
				$(\'<a class="prev-listing listing-toggle" href="\' + data.prev + \'"><i class="icon-chevron-left"></i> <span>Prev</span></a>\').prependTo($wrap);
			}
			if (data.next) {
				$(\'<a class="next-listing listing-toggle" href="\' + data.next + \'"><span>Next</span> <i class="icon-chevron-right"></i></a>\').appendTo($wrap);
			}
		}
	});', 'dynamic', false);

}
