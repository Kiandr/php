/* <script> */
(function () {
	'use strict';

	// Shorten listing remarks
	$('#listing-body .remarks').Truncate();

	// Save to Favorites
	$('#action-favorite').on('click', function () {
		var $this = $(this)
			, $icon = $this.find('i')
			, $text = $this.find('span')
			, data = $this.data('save')
		;
		$('#listing-details').Favorite({
			mls: data.mls,
			feed: data.feed,
			onComplete: function (response) {
				if (response.added) {
					$icon.attr('class', 'icon-star');
					$this.addClass('saved');
					$text.html(data.remove);
				}
				if (response.removed) {
					$icon.attr('class', 'icon-star-empty');
					$this.removeClass('saved');
					$text.html(data.add);
				}
			}
		});
		return false;
	});

	// Sticky header
	var $sticky = $('#sticky-details')
	if ($sticky.length > 0) {
		var offset = $sticky.offset().top;
		$(window).on('scroll', function () {
			var scrollTop = $(window).scrollTop();

			var is_iOS = /(iPad|iPhone|iPod)/g.test( navigator.userAgent );

			if (is_iOS && $('#sub-quicksearch input').is(':focus') && Math.abs(window.orientation) === 90) {
				if ($('#mast-wrap').hasClass('hidden') === false) {
					$('#mast-wrap').addClass('hidden');
				}
			} else {
				if ($('#mast-wrap').hasClass('hidden')) {
					$('#mast-wrap').removeClass('hidden');
				}
				$('body').toggleClass('sticky-details', scrollTop > offset);
			}
		}).trigger('scroll');
	}

	// Team Agents Carousel
	var $agents_wrap = $('.agent-details');
	var $team_carousel = $agents_wrap.find('.carousel');
	if ($team_carousel.length > 0) {
		$team_carousel.Carousel({columns: 1});
		$agents_wrap.show();
	}

})();