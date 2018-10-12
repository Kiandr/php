/* <script> */
(function () {

	// Load thumbnails
	var $module = $('.community-results').Images({
		onLoad : false
	});

	// Truncate listing detail remarks
	$('.community-results .description').Truncate({
		count: 200,
		moreText: '(more)',
		lessText: '(less)',
	});

})();