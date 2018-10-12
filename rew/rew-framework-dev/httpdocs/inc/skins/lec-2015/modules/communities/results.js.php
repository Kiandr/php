/* <script> */
(function () {

	// Load thumbnails
	var $module = $('.community-results').Images({
		onLoad : false
	});

	// Setup tagged communities mapping
	var $tagged = $('[data-tagged]')
		, tagged = {}
	;
	$tagged.each(function () {
		var $tagged = $(this)
			, tagset = $tagged.data('tagged')
		;
		$.each(tagset, function (i, tag) {
			tagged[tag] = tagged[tag] || [];
			tagged[tag].push($tagged);
		});
	});

	// Filter text
	var $filterTag = $('.filterText')
		, $filters = $('a[data-tag]')
	;

	// Filter by tag
	$filters.on('click', function () {
		var $link = $(this)
			, tag = $link.data('tag')
		;

		// Toggle active state
		$link.addClass('active').siblings('a').removeClass('active');

		// Show all
		if (tag.length < 1) {
			$filterTag.text('All');
			$tagged.removeClass('hidden');
			return;
		}

		// Hide/show communities
		$tagged.addClass('hidden');
		$.each(tagged[tag], function (i, $community) {
			$community.removeClass('hidden');
		});

		// Heading text
		$filterTag.text(tag);

	});

	// Apply current filter from hash
	var hash = window.location.hash;
	if (hash.length > 0) {
		hash = hash.substr(1);
		$filters.filter('[data-tag="' + hash + '"]').trigger('click');
	}

})();