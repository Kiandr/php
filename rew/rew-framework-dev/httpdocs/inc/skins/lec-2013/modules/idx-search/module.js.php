<?php

// Load Module's Default Javascript
$javascript = $this->locateFile('module.js.php', __FILE__);
if (!empty($javascript)) {
	require_once $javascript;
}

?>
/* <script> */
(function () {

	// Search Form
	var $form = $('.idx-search');

	// Show Advanced Criteria
	$form.find('.action-toggle-more').on('click', function() {
		var $this = $(this), $more = $form.find('.more');
		if ($more.hasClass('show')) {
			$this.removeClass('show').text('More Search Options');
			$more.removeClass('show');
		} else {
			$this.addClass('show').text('Less Search Options');
			$more.addClass('show');
		}
		return false;
	});

	// Location Check
	$form.on('toggleLocations', function () {

		// Map Search Criteria
		var mapSearch = $form.triggerHandler('checkMap');

	    // Quick Search Fields
		$('#search_mast').find(':input.location').prop('disabled', mapSearch);

	}).trigger('toggleLocations');

})();
/* </script> */