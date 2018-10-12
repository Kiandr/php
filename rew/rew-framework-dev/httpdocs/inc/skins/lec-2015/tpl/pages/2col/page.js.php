/* <script> */
(function () {
	'use strict';

	// Sidebar Toggle
	var title = 'Navigation', $body = $('body');
	if (!$body.is('.idx') && $('#sidebar').length > 0) {

		// Blog
		if ($body.is('.blog')) {
			title = 'Blog Navigation';

		// Directiory
		} else if ($body.is('.directory')) {
			title = 'Directory Categories';

			// Realty Trac
		} else if ($body.is('.rt')) {
			title = 'Search Form';
		}

		// Toggle Button
		var $toggle = $('<a id="toggle-sidebar" class="btn">' + title + '</a>').prependTo('#content');
		$toggle.on(BREW.mobile ? 'touchstart' : 'click', function () {
			$('#sidebar').toggleClass('open');
			return false;
		});

		// Close Sidebar
		var $close = $('<div class="btnset close"><a class="btn"><i class="icon-remove"></i></a></div>').appendTo('#sidebar');
		$close.on(BREW.mobile ? 'touchstart' : 'click', 'a', function () {
			$('#sidebar').removeClass('open');
		});

		// Open Sidebar
		var check = '/idx/';
		if (window.location.href.indexOf(check) === window.location.href.length - check.length) {
			$('#sidebar').addClass('open');
		}

	}

})();
/* </script> */