<?php

// Actions
$actions = array();
$actions[] = array('link' => $listing['url_inquire'], 'title' => 'Inquire', 'class' => 'popup');
$actions[] = array('link' => $listing['url_inquire'] . "?inquire_type=Property+Showing", 'title' => 'Request Showing', 'class' => 'popup');
if (empty(Settings::getInstance()->MODULES['REW_IDX_SOCIAL_NETWORK'])) {
	$actions[] = array('link' => 'mailto:?subject=Listing from ' . $_SERVER['HTTP_HOST'] . ' - ' . $listing['Address'] . '&body=While searching for property on ' . Settings::getInstance()->SETTINGS['URL_IDX'] . ', I thought you might be interested in the following property ' . Lang::write('MLS_NUMBER') . $listing['ListingMLS'] . '.%0D%0A%0D%0AThe URL below will take you to the property\'s details:%0D%0A' . $listing['url_details'], 'title' => 'Share this Listing');
} else {
	$actions[] = array('link' => $listing['url_sendtofriend'], 'title' => 'Share this Listing', 'class' => 'popup');
}
$actions[] = array('link' => $listing['url_brochure'], 'title' => 'Print this Listing', 'target' => '_blank');

// Virtual Tour Link
if (!empty($listing['VirtualTour'])) {
	$actions[] = array('link' => $listing['VirtualTour'], 'title' => 'Virtual Tour', 'target' => '_blank');
}

// Send to phone
if (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO']) {
	$actions[] = array('link' => $listing['url_phone'], 'title' => 'Send to Mobile Device', 'class' => 'popup');
}

// Display Action Buttons.
echo '<div class="nav horizontal" id="listing-nextsteps">';
echo '<h4>Next Steps:</h4>';
echo '<ul>';
foreach ($actions as $k => $action) {
	echo '<li><a class="action' . $class . (!empty($action['class']) ? ' ' . $action['class'] : '') // Class
	. '" href="' . $action['link'] . '"' // Href
	. (!empty($action['target']) ? ' target="' . $action['target'] . '"' : '') // Target
	. ' rel="nofollow">' . $action['title'] . '</a></li>' . PHP_EOL; // Text
}
echo '</ul>';
echo '</div>';