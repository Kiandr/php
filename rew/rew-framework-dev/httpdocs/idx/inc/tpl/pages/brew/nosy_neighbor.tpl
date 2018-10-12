<?php

// Listing Not Found
if (empty($property)) {
	echo '<h1>Property Record Not Found</h1>';
	echo '<p class="msg negative">The selected public record property could not be found.</p>';

} else {

	// Details Tabset
    if (Settings::getInstance()->SKIN != 'lec-2013') {
		include $page->locateTemplate('idx', 'misc', 'details');
	}

	// Require Points
	if (!empty($points)) {

		// Map Container
		echo '<div id="map_canvas"></div>';

		// Marker Legend
		echo '<div class="radius-selection colset-3">'
			. '<h4>Change Nearby Distance</h4>'
			. ((!empty($last_closest_neighbor_ft))
				? '<a class="radius-change col colset-1-sm" href="javascript:void(0);" data-distance="' . $last_closest_neighbor_ft . '"><span style="width:100%;display:block;"><img src="/img/map/rt/marker-home-green@2x.png" width="22px"> <i>View Immediate ' . Locale::spell('Neighbors') . '</i></span></a>'
				: '')
			. (($last_closest_neighbor_ft < 134.112)
				? '<a class="radius-change col colset-1-sm" href="javascript:void(0);" data-distance="134.112"><span style="width:100%;display:block;"><img src="/img/map/rt/marker-home-yellow@2x.png" width="22px"> <i>View Public Listings within 440 Feet</i></span></a>'
				: '')
			. (($last_closest_neighbor_ft < 268.224)
				? '<a class="radius-change col colset-1-sm" href="javascript:void(0);" data-distance="268.224"><span style="width:100%;display:block;"><img src="/img/map/rt/marker-home-red@2x.png" width="22px"> <i>View Public Listings within 880 Feet</i></span></a>'
				: '')
			. '</div>';

		// Title
		echo '<div id="map_info">'
			. '<h2 class="miles_from">You are viewing properties within <span class="distance">' . (!empty($last_closest_neighbor) ? ceil($last_closest_neighbor * 5280) : '440') . '</span> Feet from ' . $property['SitusAddress'] . '</h2>'
			. '</div>';

		// Show the closest 5 properties in a list
		if (!empty($closest_neighbors) && !empty($properties)) {
			echo '<div class="distance-section">';
			if (count($closest_neighbors) > 1) {
				echo '<h4>Closest ' . count($closest_neighbors) . ' ' . Locale::spell('Neighbor') . 'ing Properties</h4>';
			} else {
				echo '<h4>Closest ' . Locale::spell('Neighbor') . 'ing Propertie</h4>';
			}
			echo '<table>'
				. '<thead><tr>'
				. '<td>Address</td>'
				. '<td>Bedrooms</td>'
				. '<td>Bathrooms</td>'
				. '<td>Distance from MLS Listing</td>'
				. '<td>Direction from MLS Listing</td>'
				. '</tr></thead>'
				. '<tbody>';
			foreach ($closest_neighbors as $id => $distance) {
				echo '<tr class="nearby-property" data-id="' . $properties[$id]['Property'][0]['PropertyRTID'] . '">'
						. '<td>' . $properties[$id]['Property'][0]['SitusAddress'] . ', ' . $properties[$id]['Property'][0]['SitusCity'] . '</td>' // address
						. '<td>' . $properties[$id]['Property'][0]['Bedrooms'] . '</td>' // bedrooms
						. '<td>' . $properties[$id]['Property'][0]['Bathrooms'] . '</td>' // bathrooms
						. '<td>' . round($distance * 3280.84) . ' FT</td>'; // distance

						$p2 = array(
								'lat' => $properties[$id]['Property'][0]['Latitude'],
								'lng' => $properties[$id]['Property'][0]['Longitude']
						);

						echo '<td>' . \RealtyTrac\Integration::calculate_cardinal_direction($p1, $p2) . '</td>'; // direction

					echo '</tr>';
			}
			echo '</tbody>'
				. '</table>';
			echo '</div>';
		}

	// No Geopoints
	} else {
		echo '<p class="msg negative">This property is not able to be mapped at this time.</p>';
	}

}