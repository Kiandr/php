/* <script> */
(function () {
	'use strict';

	// Load section photos (exclude on mobile)
	var mobileWidth = 480
		, tabletWidth = 768
		, mobileLoaded = false
		, tabletLoaded = false
	;
	$(window).bind('smartresize', function () {
		var windowWidth = $(window).width();
		if (!mobileLoaded && windowWidth > mobileWidth) {
			mobileLoaded = true;
			$('img.hidden-mobile').Images({
				onLoad: false
			});
		}
		if (!tabletLoaded && windowWidth > tabletWidth) {
			tabletLoaded = true;
			$('img.hidden-tablet').Images({
				onLoad: false
			});
		}
		if (mobileLoaded && tabletLoaded) {
			$(window).off('smartresize');
		}
	}).trigger('smartresize');

	// Defer image loading
	$('#page img').not('.hidden-mobile, .hidden-tablet, .listing img').Images({
		onLoad: false
	});

})();
/* </script> */