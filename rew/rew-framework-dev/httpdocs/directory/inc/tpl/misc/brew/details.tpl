<h1><?=Format::htmlspecialchars($entry['business_name']); ?></h1>

<div id="directory-details">
    <?php if (!empty($entry['logo'])) { ?>
	    <div class="logo x4"><img src="/thumbs/190x100/<?=$entry['logo']; ?>" alt=""></div>
		<div class="keyvalset x8 last">
	<?php } else { ?>
		<div class="keyvalset x12 last">
	<?php } ?>
		<ul>
			<?php if (!empty($entry['address'])) { ?>
				<li class="keyval">
					<strong>Address</strong>
					<span><?=Format::htmlspecialchars($entry['address']); ?></span>
				</li>
			<?php } ?>
			<?php if (!empty($entry['phone'])) { ?>
				<li class="keyval">
					<strong>Phone #</strong>
					<span><?=Format::htmlspecialchars($entry['phone']); ?></span>
				</li>
			<?php } ?>
			<?php if (!empty($entry['alt_phone'])) { ?>
				<li class="keyval">
					<strong>Secondary Phone #</strong>
					<span><?=Format::htmlspecialchars($entry['alt_phone']); ?></span>
				</li>
			<?php } ?>
			<?php if (!empty($entry['toll_free'])) { ?>
				<li class="keyval">
					<strong>Toll Free #</strong>
					<span><?=Format::htmlspecialchars($entry['toll_free']); ?></span>
				</li>
			<?php } ?>
			<?php if (!empty($entry['fax'])) { ?>
				<li class="keyval">
					<strong>Fax #</strong>
					<span><?=Format::htmlspecialchars($entry['fax']); ?></span>
				</li>
			<?php } ?>
			<?php if (!empty($entry['website'])) { ?>
				<li class="keyval">
					<strong>Website</strong>
					<span><?=$entry['website']; ?></span>
				</li>
			<?php } ?>
		</ul>
	</div>
	<div class="description"><?=$entry['description']; ?></div>
	<?php

		// Include Photo Gallery
		include $page->locateTemplate('directory', 'misc', 'gallery');

	?>
</div>
<?php

// Show Map
if (!empty($entry['latitude']) && !empty($entry['longitude']) && !empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) {

	// Map Container
	echo '<div id="directory-map"></div>';

	// Map Options
	$map = json_encode(array(
		'streetview' => !empty(Settings::getInstance()->MODULES['REW_IDX_STREETVIEW']),
		'center' => array('lat' => $entry['latitude'], 'lng' => $entry['longitude']),
		'manager' => array('bounds' => false)
	));

	// Start Javascript
	ob_start();

?>
/* <script> */

	// Load Map
	var $map = $('#directory-map').REWMap($.extend(<?=$map; ?>, {
		onInit : function () {

			// Marker Icon
			var icon = new google.maps.MarkerImage('/img/map/marker-shopping@2x.png', null, null, null, new google.maps.Size(20, 25));

			// Add Marker to Map
			var marker = new REWMap.Marker({
				'map' : $map.data('Map'),
				'icon' : icon,
				'lat' : <?=floatval($entry['latitude']); ?>,
				'lng' : <?=floatval($entry['longitude']); ?>
			});

		}
	}));

/* </script> */
<?php

	// Write Javascript
	$page->writeJS(ob_get_clean());

}