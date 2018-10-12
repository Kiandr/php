<?php

/* @todo: this get local tab should show icon to indicate popup - OR - dont open in popup? */
/* @todo: include needed compliance requirements */

// Listing Not Found
if (empty($listing)) {
	echo '<div class="wrap">';
	echo '<h1>Listing Not Found</h1>';
	echo '<p class="msg negative">The selected listing could not be found. This is probably because the listing has been sold, removed from the market, or simply moved.</p>';
	echo '</div>';
	return;
}

// Skin URL
$skinUrl = $page->getSkin()->getUrl();

// Show map & directions
$show_map = !empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING']) && !empty($listing['Latitude']) && !empty($listing['Longitude']);

// Show google streetview
$streetview = !empty(Settings::getInstance()->MODULES['REW_IDX_STREETVIEW']) && !empty($listing['Latitude']) && !empty($listing['Longitude']);

// Show bird's eye view
$birdseye = !empty(Settings::getInstance()->MODULES['REW_IDX_BIRDSEYE']) && !empty($listing['Latitude']) && !empty($listing['Longitude']);

// Show get local link
$onboard = !empty(Settings::getInstance()->MODULES['REW_IDX_ONBOARD']);

// Details page javascript
$page->addJavascript('js/idx/details.js', 'page')
	// MAP_OPTIONS
	->addJavascript('var MAP_OPTIONS = ' . json_encode(array(
		'zoom' => 14,
		'streetview' => $streetview,
		'center' => array(
			'lat' => $listing['Latitude'],
			'lng' => $listing['Longitude']
		),
		'manager' => array(
			'icon' => $skinUrl . '/img/map-ico.png',
			'iconWidth' => 21,
			'iconHeight' => 26,
			'bounds' => false,
			'markers' => array(array(
				'tooltip' => call_user_func(function ($listing, $page) {
					ob_start();
					$listing_tooltip = $listing;
					include $page->locateTemplate('idx', 'misc', 'tooltip');
					return str_replace(array("\r\n", "\n", "\t"), "", ob_get_clean());
				}, $listing, $page),
				'lat' => $listing['Latitude'],
				'lng' => $listing['Longitude']
			))
		)
	)) . ';', 'dynamic', false)
	// IDX_LISTING
	->addJavascript('var IDX_LISTING = ' . json_encode(array(
		'feed'	=> $listing['idx'],
		'mls'	=> $listing['ListingMLS'],
		'lng'	=> $listing['Longitude'],
		'lat'	=> $listing['Latitude']
	)) . ';', 'dynamic', false)
;

// New listing
$new = !is_null($listing['ListingDOM']) && $listing['ListingDOM'] <= 7;

// Price reduced
$reduced = !empty($listing['ListingPriceOld']) && ($listing['ListingPrice'] < $listing['ListingPriceOld']);
$reduced = $reduced ? abs(round((($listing['ListingPrice'] - $listing['ListingPriceOld']) / $listing['ListingPriceOld']) * 100)) : NULL;

// Listing title
$address = array();
$address[] = !empty($listing['Address']) || $listing['Address'] === 'N/A' ? $listing['Address'] : '(Undisclosed Address)';
$address[] = $listing['AddressCity'];
$address[] = $listing['AddressZipCode'];
$title = implode(', ', array_filter($address));

// Listing basics
$basics = array();
$basics[] = array($listing['NumberOfBedrooms'] > 0 ? $listing['NumberOfBedrooms'] : '&ndash;', Format::plural($listing['NumberOfBedrooms'], 'Beds', 'Bed'));
$basics[] = array($listing['NumberOfBathrooms'] > 0 ? Format::fraction($listing['NumberOfBathrooms']) : '&ndash;', Format::plural($listing['NumberOfBathrooms'], 'Baths', 'Bath'));
$basics[] = array($listing['NumberOfSqFt'] > 0 ? Format::number($listing['NumberOfSqFt']) : '&ndash;', 'Sqft');
if ($listing['NumberOfAcres'] > 0)		$basics[] = array(Format::fraction($listing['NumberOfAcres']), 'Acres');
if ($listing['NumberOfGarages'] > 0)	$basics[] = array(Format::fraction($listing['NumberOfGarages']), 'Garages');
$basics[] = array($listing['YearBuilt'] > 0 ? $listing['YearBuilt'] : '&ndash;', 'Built');
if ($listing['ListingDOM'] > 0)			$basics[] = array(Format::fraction($listing['ListingDOM']), 'DOM');

// Extended details
$extended = array();
if ($details = $idx->getDetails()) {

	// MLS Compliance (Agent / Office)
	if ( empty($_COMPLIANCE['details']['show_below_details']) ) {
		if (empty($_COMPLIANCE['details']['show_below_remarks']) && (!isset($_COMPLIANCE['details']['provider_first']) || $_COMPLIANCE['details']['provider_first']($listing) == false) ){
			if (is_callable($_COMPLIANCE['details']['extra'])) {
				if ($details_extra = $_COMPLIANCE['details']['extra']($idx, $db_idx, $listing, $_COMPLIANCE)) {
					foreach ($details_extra as $extra) {
						if (!empty($_COMPLIANCE['details']['details_provider_first'])) {
							array_unshift($details, $extra);
						} else {
							array_push($details, $extra);
						}
					}
				}
			} else {
				$provider_info = array('heading' => (!empty($_COMPLIANCE['details']['lang']['listing_details']) ? $_COMPLIANCE['details']['lang']['listing_details'] : 'Listing Details'), 'fields' => array(
					!empty($_COMPLIANCE['details']['show_agent']) ? array('title' => 'Listing Agent', 'value' => 'ListingAgent') : null,
					!empty($_COMPLIANCE['details']['show_office']) ? array('title' => (!empty($_COMPLIANCE['details']['lang']['provider']) ? $_COMPLIANCE['details']['lang']['provider'] : 'Listing Office'), 'value' => 'ListingOffice') : null,
					!empty($_COMPLIANCE['details']['show_office_phone']) ? array('title' => 'Office Phone', 'value' => 'ListingOfficePhoneNumber') : null,
					!empty($_COMPLIANCE['details']['show_icon']) ? array('block' => $_COMPLIANCE['details']['show_icon']) : false
				));
				if (!empty($_COMPLIANCE['details']['details_provider_first'])) {
					array_unshift($details, $provider_info);
				} else {
					array_push($details, $provider_info);
				}
			}
		}
	}

	foreach ($details as $info) {
		$fields = array();
		foreach ($info['fields'] as $field) {
			if ($value = $listing[$field['value']]) {
				if ($field['format']) $value = tpl_format($value, $field['format']);
				if (empty($value)) continue;
				$fields[] = array('title' => $field['title'], 'value' => $value);
			} else if (!empty($field['block'])) {
				$fields[] = array('title' => '', 'value' => $field['block']);
			}
		}
		if (empty($fields)) continue;
		$extended[] = array('heading' => $info['heading'], 'fields' => $fields);
	}
}

// Financial calculations
if (!empty($listing['NumberOfSqFt'])) {
	$price_per_sqft = $listing['ListingPrice'] / $listing['NumberOfSqFt'];
	if (!empty($price_per_sqft)) {
		$extended[] = array('heading' => 'Financials', 'fields' => array(
			array('title' => '$/SqFt', 'value' => '$' . Format::number($price_per_sqft))
		));
	}
}

#listing-details class name
$classList = array();
if (!empty($dismissed)) $classList[] = 'dismissed';
if (!empty($bookmarked)) $classList[] = 'saved';

?>
<div class="wrap">
	<div class="deck">
        <?php
            if(!empty($_COMPLIANCE['details']['provider_first']) && $_COMPLIANCE['details']['provider_first']($listing)) {
                \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing);
            }
        ?>
		<h1><?=Format::htmlspecialchars($title); ?></h1>
		<h4>
			<?php

				// Compliance Auction Banner Display
				if (is_callable($_COMPLIANCE['is_up_for_auction']) && $_COMPLIANCE['is_up_for_auction']($listing)) {
					echo '<span class="flag flag-auction">AUCTION</span>';
				// Flags for new listings / price reduction
				} else if ($reduced > 0 && $_COMPLIANCE['flags']['hide_price_reduction'] != true) {
					echo '<span class="flag flag-reduced">REDUCED ' . $reduced . '% </span>';
				} else if (!empty($new)) {
					echo '<span class="flag flag-new">NEW</span>';
				}

				// Define rental type array
				$rental_types = array('Rental', 'Rentals', 'Lease', 'Residential Lease', 'Commercial Lease', 'Residential Rental');

				// Property type title
				echo implode(' - ', array_filter(array($listing['ListingType'], $listing['ListingSubType'])));
				echo " " . (in_array($listing['ListingType'],$rental_types) ? "" : "For Sale");
				if (!empty($listing['AddressSubdivision'])) echo ' in ' . $listing['AddressSubdivision'];

			?>

<?
	// Back URL
	$url_back = ($user->url_back() ? Http_Uri::getScheme() . "://" . $_SERVER['HTTP_HOST'] . $user->url_back() : Settings::getInstance()->SETTINGS['URL_IDX']);

?>

			<div class="nav horizontal">
				<ul id="listing-pagination">
					<li class="navi-results"><a href="<?=$url_back; ?>"><i class="icon-arrow-left"></i> <span>Back to Results</span></a></li>
				</ul>
			</div>

		</h4>
	</div>
	<div id="listing-details"<?=(!empty($classList) ? ' class="' . implode(' ', $classList) . '"' : ''); ?>>
		<div id="listing-gallery">
			<ul class="tabset views">
				<li class="current"><a data-target="photos">Photos</a></li>
				<?php if (!empty($show_map)) { ?>
					<li><a data-target="map">Map</a></li>
				<?php } ?>
				<?php if (!empty($streetview) && !empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) { ?>
					<li class="hidden"><a data-target="streetview">Streetview</a></li>
				<?php } ?>
				<?php if (!empty($birdseye)) { ?>
					<li><a data-target="birdseye">Bird's Eye</a></li>
				<?php } ?>
				<?php if (!empty($onboard) && !empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) { ?>
					<li><a href="<?=$listing['url_onboard']; ?>">Get Local</a></li>
				<?php } ?>
			</ul>
			<div class="tabs">

				<?php // Photo Gallery ?>
				<div id="tab-photos" class="tab">
					<?php $this->container('gallery')->module('gallery', array(
						'images'	=> $listing['thumbnails'],
						'enlarge'	=> false,
						'links'		=> NULL
					))->display(); ?>

					<div class="actions">
						<a id="action-hide" rel="nofollow" title="Hide this Property" data-hide='<?=json_encode(array('feed' => $listing['idx'], 'mls' => $listing['ListingMLS'])); ?>'>
							<i class="icon-listingHide"></i>
						</a>
						<a id="action-save" rel="nofollow" title="Save this Property" data-save='<?=json_encode(array('feed' => $listing['idx'], 'mls' => $listing['ListingMLS'])); ?>'>
							<i class="icon-listingFav"></i>
						</a>
					</div>

				</div>

				<?php // Map & Directions ?>
				<?php if (!empty($show_map)) { ?>
					<div id="tab-map" class="tab hidden">
						<div id="map-canvas"></div>
						<?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_DIRECTIONS'])) { ?>
							<div id="map-directions" class="msg">
								<form>
									<strong>Get directions to this property:</strong>
									<input name="from" placeholder="From Address&hellip;" required>
									<input type="hidden" name="to" value="<?=Format::htmlspecialchars(!empty($listing['Latitude']) && !empty($listing['Longitude']) ? $listing['Latitude'].','.$listing['Longitude'] : $listing['Address'] . ', ' . $listing['AddressCity'] . ' ' . $listing['AddressZipCode']); ?>">
									<div class="btnset"><button class="strong" type="submit">Get Directions</button></div>
								</form>
								<div id="directions"></div>
							</div>
						<?php } ?>
					</div>
				<?php } ?>

				<?php // Google Streetview ?>
				<?php if (!empty($streetview)) { ?>
					<div id="tab-streetview" class="tab hidden">
						<div style="position: relative !important;">
							<div id="map-streetview"></div>
						</div>
					</div>
				<?php } ?>

				<?php // MSVE Bird's Eye View ?>
				<?php if (!empty($birdseye)) { ?>
					<div id="tab-birdseye" class="tab hidden">
						<div style="position: relative !important;">
							<div id="map-birdseye"></div>
						</div>
					</div>
				<?php } ?>

			</div>
		</div>
		<div id="listing-body">

			<h3>
				$<?=Format::number($listing['ListingPrice']); ?>
				<?php if (!in_array($listing['ListingType'],$rental_types)) { ?>
				<a data-dialog="#dialog-calc">
					<span class="calc-payment"></span>
					<i class="icon-listingCalc"></i>
				</a>
				<?php } ?>
			</h3>

			<div class="hr"></div>

			<?php

				// Property basics
				if (!empty($basics)) {
					echo '<div class="keyvalset">';
					foreach ($basics as $info) {
						list ($value, $unit) = $info;
						echo '<div class="keyval">';
						echo '<span class="val">' . $value . '</span>';
						echo '<span class="key">' . $unit . '</span>';
						echo '</div>';
					}
					echo '</div>';
				}

			?>

			<div class="hr"></div>

			<?php $listingMLS = ($idx->getName() == 'cms') ? $listing['ListingMLSNumber'] : $listing['ListingMLS']; ?>
			<?php if (!empty($listingMLS)) echo '<h5 style="margin: 0;"><strong>MLS&reg; #: ' . $listingMLS . '</strong></h5>'; ?>

			<div>
				<?=($listing['ListingStatus'] . (!empty($listing['ListingDOM']) ? ', ' . Format::number($listing['ListingDOM']) . ' Days on Market' : '')); ?>
			</div>

			<div class="hr spacer"></div>

			<div class="actions" style="margin-bottom: 1px;">
				<a class="popup strong" title="Find out more" href="<?=$listing['url_inquire']; ?>">Inquire</a>
				<a class="popup" title="Share this listing" href="<?=$listing['url_sendtofriend']; ?>">Share</a>
				<a target="_blank" title="Print this listing" href="<?=$listing['url_brochure']; ?>">Print</a>
				<?php if (!empty($listing['VirtualTour'])) { ?>
					<a target="_blank" title="Virtual Tour" href="<?=$listing['VirtualTour']; ?>">Tour</a>
				<?php } ?>
			</div>

			<?php if (!empty(Settings::getInstance()->MODULES['REW_RT']) && !empty($listing['AddressState'])) { ?>
				<div class="actions" style="margin-bottom: 1px;">
					<?php if ($rt_link = \RealtyTrac\Integration::get_rt_from_mls($idx->getName(), $listing['ListingMLS'], $listing['AddressState'])) { ?>
						<a rel="nofollow" title="View Public Record" href="<?=$rt_link; ?>"><i class="icon-search"></i> View Public Record</a>
					<?php } ?>
				</div>
			<?php } ?>

			<div class="actions">

                <?php if (!empty(Settings::getInstance()->MODULES['REW_RT'])) { ?>
                    <?php
                    // Property ID
                    $rtid = \RealtyTrac\Integration::get_rt_from_mls($idx->getName(), $listing['ListingMLS'], $listing['AddressState'], true);
                    ?>
                    <?php if (!empty($listing['AddressState'])) { ?>
                        <?php if (!empty(Settings::getInstance()->MODULES['REW_RT_NEARBY_SOLDS']) && !empty($rtid)) { ?>
                            <a class="popup" title="Nearby Public Record Solds" href="<?=$listing['url_details']; ?>nearby_solds/" rel="nofollow">Nearby Solds</a>
                        <?php } ?>
                        <?php if (!empty(Settings::getInstance()->MODULES['REW_RT_NOSY_NEIGHBOR']) && !empty($rtid) && !empty($listing['Latitude']) && !empty($listing['Longitude'])) { ?>
                            <a class="popup" title="Nearby Public Record Neighbors" href="<?=$listing['url_details']; ?>nosy_neighbor/" rel="nofollow">Nosy <?=Locale::spell('Neighbor'); ?></a>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>

			</div>

			<p class="remarks"><?=($idx->getLink() == 'cms') ? nl2br(htmlspecialchars($listing['ListingRemarks'])) : htmlspecialchars($listing['ListingRemarks']); ?></p>

		<?php if (!empty($_COMPLIANCE['details']['show_below_remarks'])) { ?>
				<p class="remarks">
				<?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing); ?>
				</p>
		<?php } ?>

		<?php if (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO']) { ?>
			<div class="hidden-phone">
				<div class="btnset actions">
					<a rel="nofollow" class="btn popup" href="<?=$listing['url_phone']; ?>">Send to Mobile Device</a>
				</div>
			</div>
		<?php } ?>


		</div>
	</div>
</div>

<div id="extended-details">
	<div class="wrap">
		<div class="dataset">
			<?php

				// Price Change History
				include $page->locateTemplate('idx', 'misc', 'history');

				// Extended details
				if (!empty($extended)) {
					foreach ($extended as $details) {
						echo '<section>';
						echo '<h3>' . Format::htmlspecialchars($details['heading']) . '</h3>';
						echo '<div class="colset colset-1-sm colset-3-md colset-3-lg colset-3-xl">';
						foreach ($details['fields'] as $field) {
							echo '<div class="col">';
							if (!empty($field['title'])) {
							    echo '<strong>' . Format::htmlspecialchars($field['title']) . ':</strong>' . PHP_EOL;
							}
							if (strpos($field['value'], "<img") !== false) {
							    echo $field['value'];
							} else {
							    echo Format::htmlspecialchars($field['value']);
							}
							echo '</div>';
						}
						echo '</div>';
						echo '</section>';
					}
				}

			?>
		</div>
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
</div>
<?php

// Mortgage calculator settings
$settings = $this->skin->getSettings();

if (!in_array($listing['ListingType'],$rental_types)) {
	// Listing mortgage calculator
	$this->container('listing-calculator')->module('listing-calculator', array(
		'listing_price'	=> $listing['ListingPrice'],
		'down_percent'	=> $settings['down_percent'],
		'interest_rate'	=> $settings['interest_rate'],
		'mortgage_term'	=> $settings['mortgage_term']
	))->display();
}

// Include similar listings
$this->container('listing-similar')->module('listing-similar', array(
		'uid'		=> 'similar-listings',
		'listing'	=> $listing
))->display();
