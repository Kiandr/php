/* <script> */
(function () {
	'use strict';

	// Search Form
	var $form = $('#agent-form');

	// Toggle drop down display
	$form.on('click', '.dropdown-title', function () {
		$(this).closest('li').toggleClass('active').siblings('li').removeClass('active');
	});

	// Submit form on office filter
	$form.on('change', 'input[name="office"]', function () {
		window.location.href = '?office=' + this.value;
		return false;
	});

})();
/* </script> */