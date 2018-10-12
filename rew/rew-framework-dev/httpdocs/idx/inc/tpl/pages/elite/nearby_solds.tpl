<?php

// Listing Not Found
if (empty($property)) {
    echo '<h1>Property Record Not Found</h1>';
    echo '<p class="uk-alert uk-alert-danger">The selected public record property could not be found.</p>';
    return;
}

// Require Points
if (empty($points)) {
    echo '<p class="uk-alert uk-alert-danger">This property is not able to be mapped at this time.</p>';
    return;
}

// Title
echo '<div id="map_info">'
    . '<h2 class="miles_from">You are viewing comparable properties within <span class="miles">0.25</span> miles from ' . $property['SitusAddress'] . '</h2>'
    . '</div>';

// Map Container
echo '<div class="fw-idx-map"></div>';

// If we are on popup we need to add a module
if (isset($_GET['popup'])) {
    $page->container('snippet')->addModule('rt-sold-search', array(
            'state' => $listing['AddressState'],
    ))->display();
}
