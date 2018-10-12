/* <script> */
(function () {

	// Truncate Agent Bio
	$('.description-cut').Truncate();

	// Tabset behaviour
	$('.tabset--behaviour').on('click', 'a', function () {
		var $link = $(this)
			, href = $link.attr('href')
			, $parent = $link.parent()
		;
		$parent.addClass('current').siblings().removeClass('current')
		$(href).removeClass('hidden').siblings().addClass('hidden');
		return false;
	}).find('a:first').trigger('click');

})();
/* </script> */