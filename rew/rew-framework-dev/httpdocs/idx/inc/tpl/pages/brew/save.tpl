<?php

// Listing Not Found
if (empty($listing)) {
	echo '<h1>Listing Not Found</h1>';
	echo '<p class="msg negative">The selected listing could not be found. This is probably because the listing has been sold, removed from the market, or simply moved.</p>';

} else {

	// Success
	if ($success) {
		echo '<p class="msg positive">The selected listing has successfully been saved.</p>';

	    // Write Javascript
		$page->writeJS("$(window.parent.document).find('#listing-" . $listing['ListingMLS'] . "').addClass('saved');");

	// Error
	} else {
        echo '<p class="msg negative">' . $errors[0] . '</p>';

    }
}

?>