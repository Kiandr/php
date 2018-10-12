<?php

// AJAX Load
if (!empty($_POST['ajax'])) ob_start();

// Listing Not Found
if (empty($listing)) {
	echo '<h1>Listing Not Found</h1>';
	echo '<p class="msg negative">The selected listing could not be found. This is probably because the listing has been sold, removed from the market, or simply moved.</p>';

} else {

	// Listing Title
	echo (!empty($listing['ListingTitle'] && !isset($_GET['popup'])) ? '<h1>' . $listing['ListingTitle'] . '</h1>' : '');

	// Details Tabset
	if (!isset($_GET['popup'])) include $page->locateTemplate('idx', 'misc', 'details');

	// Listing Heading
	if (empty($_COMPLIANCE['details']['remove_heading']) && !isset($_GET['popup'])) {
		echo '<h1>$' . Format::number($listing['ListingPrice']) . ' - ' . $listing['Address'] . ', ' . $listing['AddressCity'] . ', ' . $listing['AddressState'] . '</h1>';
	}

	// Location Unavailable
	if (empty($location)) {
		echo '<p class="msg negative">This feature is currently unavailable in this listing\'s region.</p>';

	} else {

?>

	<div id="idx-map-onboard"></div>

	<?php include $page->locateTemplate('idx', 'misc', 'nextsteps'); ?>

	<div class="tabbed-content">

		<div class="tabset">
			<ul>
				<li<?=($view == 'nearby-amenities')			? ' class="current" ' : ''; ?>><a href="?view=nearby-amenities" data-panel="#nearby-amenities">Nearby Amenities</a></li>
				<li<?=($view == 'nearby-schools')			? ' class="current" ' : ''; ?>><a href="?view=nearby-schools" data-panel="#nearby-schools">Nearby Schools</a></li>
				<li<?=($view == 'community-information')	? ' class="current" ' : ''; ?>><a href="?view=community-information" data-panel="#community-information">Neighborhood Information</a></li>
			</ul>
		</div>

		<div id="nearby-amenities" class="panel <?=($view == 'nearby-amenities') ? 'loaded' : 'hidden'; ?>">

			<div class="clear"></div>

			<?php if ($view == 'nearby-amenities') : ?>

				<?php if (!empty($_POST['ajax'])) ob_clean(); ?>

				<?php if (count($nearby_amenities) > 0) : ?>

					<table>
						<thead>
							<tr>
								<th width="10">&nbsp;</th>
								<th>Business Name</th>
								<th>Category</th>
								<th>Distance</th>
							</tr>
						</thead>
						<tbody>
						<?php $count = 0; ?>
						<?php $class = 'odd'; ?>
						<?php foreach ($nearby_amenities as $nearby_amenity) : ?>
							<?php $class = !empty($class) ? '' : ' class="odd"'; ?>
							<tr id="amenity-<?=$count; ?>" valign="top"<?=$class; ?>>
								<td width="50"><img src="/img/map/legend-shopping@2x.png" width="20" height="20" alt=""></td>
								<td>
									<a href="javascript:void(0);" onclick="amenities[<?=$count; ?>].select();"><?=ucwords(strtolower($nearby_amenity['BUSNAME'])); ?></a>
									<?=ucwords(strtolower($nearby_amenity['STREET'])); ?>. <?=ucwords(strtolower($nearby_amenity['CITY'])); ?>, <?=ucwords(strtolower($nearby_amenity['STATENAME'])); ?>.
									<?=ucwords(strtolower($nearby_amenity['PHONE'])); ?>
									<?php

									// HTML Tooltip
									$tooltip = '<div class="popover">'
										. '<header class="title">'
											. '<strong>' . ucwords(strtolower($nearby_amenity['BUSNAME'])) . '</strong>'
											. '<a class="action-close hidden" href="javascript:void(0);">&times;</a>'
										. '</header>'
										. '<div class="body">'
											. ucwords(strtolower($nearby_amenity['STREET'])) . '. ' . ucwords(strtolower($nearby_amenity['CITY'])) . '<br>'
											. ucwords(strtolower($nearby_amenity['INDUSTRY']))
										. '</div>'
										. '<div class="tail"></div>'
									. '</div>';

									?>
									<script>
									//<![CDATA[
                                    amenities.push(new REWMap.Marker({
                                        'map' : $map.data('REWMap'),
                                        'tooltip' : '<?=addslashes($tooltip); ?>',
                                        'icon' : iconShopping,
                                        'lat' : <?=floatval($nearby_amenity['LATITUDE']); ?>,
                                        'lng' : <?=floatval($nearby_amenity['LONGITUDE']); ?>,
                                        'zIndex' : 1
                                    }));
									//]]>
									</script>
								</td>
								<td><?=ucwords(strtolower($nearby_amenity['CATEGORY'])); ?></td>
								<td><?=number_format($nearby_amenity['distance'], 2); ?> mi</td>
							</tr>
							<?php $count++; ?>
						<?php endforeach; ?>
						</tbody>
					</table>

				<?php else : ?>

					<div class="msg">
						<p>No nearby amenities could be found at this time.</p>
					</div>

				<?php endif; ?>

				<?php if (!empty($_POST['ajax'])) die(ob_get_clean()); ?>

			<?php endif; ?>

		</div>

		<div id="nearby-schools" class="panel <?=($view == 'nearby-schools') ? 'loaded' : 'hidden'; ?>">

			<div class="clear"></div>

			<?php if ($view == 'nearby-schools') : ?>

				<?php if (!empty($_POST['ajax'])) ob_clean(); ?>

				<?php if (count($nearby_schools) > 0) : ?>

					<table>
						<thead>
							<tr>
								<th width="10">&nbsp;</th>
								<th>School Name</th>
								<th>Grades</th>
								<th>Distance</th>
							</tr>
						</thead>
						<tbody>
						<?php $count = 0; ?>
						<?php $class = 'odd'; ?>
						<?php foreach ($nearby_schools as $nearby_school) : ?>
							<?php $class = !empty($class) ? '' : ' class="odd"'; ?>
							<tr id="school-<?=$count; ?>" valign="top"<?=$class; ?>>
								<td width="50"><img src="/img/map/legend-school@2x.png" width="20" height="20" alt=""></td>
								<td>
									<a  href="javascript:void(0);" onclick="schools[<?=$count; ?>].select();"><?=ucwords(strtolower($nearby_school['INSTITUTION_NAME'])); ?></a><br />
									<?=ucwords(strtolower($nearby_school['LOCATION_ADDRESS'])); ?>. <?=ucwords(strtolower($nearby_school['LOCATION_CITY'])); ?>, <?=$nearby_school['STATE_ABBREV']; ?>.
									<?php if (!empty($nearby_school['WEBSITE_URL'])) : ?>
										<a href="<?=$nearby_school['WEBSITE_URL']; ?>" target="_blank" rel="nofollow">School Website</a>
									<?php endif; ?>
									<?php

										// HTML Tooltip
										$tooltip = '<div class="popover">'
											. '<header class="title">'
												. '<strong>' . $nearby_school['GRADE_SPAN_CODE_BLDG_TEXT'] . ' - ' . ucwords(strtolower($nearby_school['INSTITUTION_NAME'])) . '</strong>'
												. '<a class="action-close hidden" href="javascript:void(0);">&times;</a>'
											. '</header>'
											. '<div class="body">'
												. ucwords(strtolower($nearby_school['LOCATION_ADDRESS'])) . '<br>'
												. ucwords(strtolower($nearby_school['LOCATION_CITY'])) . ', ' . $nearby_school['STATE_ABBREV']
											. '</div>'
											. '<div class="tail"></div>'
										. '</div>';

									?>
									<script>
									//<![CDATA[
                                    schools.push(new REWMap.Marker({
                                        'map': $map.data('REWMap'),
                                        'tooltip': '<?=addslashes($tooltip); ?>',
                                        'icon': iconSchool,
                                        'lat': <?=floatval($nearby_school['LATITUDE']); ?>,
                                        'lng': <?=floatval($nearby_school['LONGITUDE']); ?>,
                                        'zIndex': 1
                                    }));
									//]]>
									</script>
								</td>
								<td><?=$nearby_school['GRADE_SPAN_CODE_BLDG_TEXT']; ?></td>
								<td><?=number_format($nearby_school['distance'], 2); ?> mi</td>
							</tr>
							<?php $count++; ?>
						<?php endforeach; ?>
						</tbody>
					</table>

				<?php else : ?>

					<div class="msg">
						<p>No nearby schools could be found at this time.</p>
					</div>

				<?php endif; ?>

				<?php if (!empty($_POST['ajax'])) die(ob_get_clean()); ?>

			<?php endif; ?>

		</div>

		<div id="community-information" class="panel <?=($view == 'community-information') ? 'loaded' : 'hidden'; ?>">

			<?php if ($view == 'community-information') : ?>

				<?php if (!empty($_POST['ajax'])) ob_clean(); ?>

				<div class="details-extended">
					<?php foreach (array_chunk($statistics, ceil(count($statistics) / 2)) as $statistics) { ?>
						<div class="col">
							<?php foreach ($statistics as $statistic) { ?>
								<div class="keyvalset">
									<?php if (!empty($statistic['title'])) { ?>
										<h3><?=$statistic['title']; ?>:</h3>
									<?php } ?>
									<ul>
										<?php foreach ($statistic['statistics'] as $stats) { ?>
											<li class="keyval">
												<strong><?=$stats['title']; ?>:</strong>
												<span><?=$stats['value']; ?></span>
											</li>
										<?php } ?>
									</ul>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
				</div>

				<?php if (!empty($_POST['ajax'])) die(ob_get_clean()); ?>

			<?php endif; ?>

		</div>

	</div>

	<p class="disclaimer">Disclaimer / Sources: <?=Lang::write('MLS'); ?> local resources application developed and powered by <a href="http://www.realestatewebmasters.com/" rel="nofollow" target="_blank">Real Estate Webmasters</a> - Neighborhood data provided by Onboard Informatics &copy; <?=date('Y'); ?> - Mapping Technologies powered by Google Maps&trade;</p>

<?php

	}

// Map Markers
$markers = array();

// Mappable Listing
if (!empty($listing['Latitude']) && !empty($listing['Longitude'])) {

	if (empty($_COMPLIANCE['local']['disable_popup'])) {
		// Tooltip
		$listing_tooltip = $listing;
		ob_start();
		include $page->locateTemplate('idx', 'misc', 'tooltip');
		$tooltip = ob_get_clean();
	}

	// Map Marker
	$markers[] = array(
		//'title' => implode(', ', array($listing['Address'], $listing['AddressCity'], $listing['AddressState'])) . html_entity_decode(' MLS&reg; #', ENT_COMPAT | ENT_HTML401, 'UTF-8') . $listing['ListingMLS'],
		'tooltip' => $tooltip,
		'lat' => $listing['Latitude'],
		'lng' => $listing['Longitude'],
		'zIndex' => 2
	);

	// Map Center
	$center = array('lat' => $listing['Latitude'], 'lng' => $listing['Longitude']);

// Use Location
} else {

	// Map Center
	$center = array('lat' => $location['LATITUDE'], 'lng' => $location['LONGITUDE']);

}

// Map Options
$map = json_encode(array(
	'streetview' => !empty(Settings::getInstance()->MODULES['REW_IDX_STREETVIEW']),
	'center' => $center,
	'manager' => array(
		'markers' => $markers,
		'bounds' => false
	)
));

// Start JS
ob_start();

?>
/* <script> */

	// Map POIs
	var amenities = [], schools = [], iconShopping, iconSchool;

	// Load Map
	var $map = $('#idx-map-onboard').REWMap($.extend(<?=$map; ?>, {
		onInit : function () {

			// Shopping Icon
			iconShopping = new google.maps.MarkerImage('/img/map/marker-shopping@2x.png', null, null, null, new google.maps.Size(20, 25));

			// School Icon
			iconSchool = new google.maps.MarkerImage('/img/map/marker-school@2x.png', null, null, null, new google.maps.Size(20, 25));

			// Activate Tab
			var $active = $tabs.find('li.current a');
			$active = ($active.length > 0) ? $active : $tabs.find('a:first');
			$active.trigger(BREW.events.click);

		}
	}));

	// Toggle Tabs
	var $tabs = $('div.tabbed-content .tabset').on(BREW.events.click, ' a', function () {
		var $this = $(this), $item = $this.parent('li');
		if (!$item.hasClass('current')) {
			// Toggle Panel
			var panel = $this.data('panel'), $panel = $(panel);
			if ($panel.length > 0) {
				$item.addClass('current').siblings('li').removeClass('current');
				$panel.removeClass('hidden');
				$panel.siblings('.panel').addClass('hidden');
			}
			// Toggle Schools
			var i = 0, l = schools.length, show = (panel == '#nearby-schools');
			for (i; i < l; i++) {
				if (show) {
					schools[i].show();
				} else {
					schools[i].hide();
				}
			}
			// Toggle Amenities
			var i = 0, l = amenities.length, show = (panel == '#nearby-amenities');
			for (i; i < l; i++) {
				if (show) {
					amenities[i].show();
				} else {
					amenities[i].hide();
				}
			}
			// Toggle Map
			var toggle = (panel == '#community-information') ? 'hide' : 'show';
			$map.REWMap(toggle, function () {
				// Already Loaded
				if ($panel.hasClass('loaded')) return false;
				$panel.html('<div class="msg"><p>Loading Results...</p></div>');
				// Load Panel...
				$.ajax({
					'url'		: '?view=' + panel.replace('#', ''),
					'type'		: 'POST',
					'data'		: 'ajax=true',
					'dataType'	: 'html',
					'success'	: function (data) {
						$panel.addClass('loaded').html(data);
					}
				});
			});
		}
		return false;
	});

/* </script> */
<?php

	// Write JS
	$page->writeJS(ob_get_clean());

	// Show MLS Office / Agent
	if (!isset($_COMPLIANCE['details']['provider_first']) || $_COMPLIANCE['details']['provider_first']($listing) == false) {
		\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing);
	}

}
