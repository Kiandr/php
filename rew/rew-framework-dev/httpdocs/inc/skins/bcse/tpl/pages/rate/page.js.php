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

	// Load audio files
	var path = '/inc/js/vendor/audiojs/';
	audiojs.events.ready(function() {
		var as = audiojs.createAll({
			imageLocation: path + 'player-graphics.gif',
			swfLocation: path + 'audiojs.swf',
			createPlayer: {
				markup: '\
					<div class="radio-title"></div>\
					<div class="scrubber">\
						<div class="scrubber-wrap">\
							<div class="progress"></div>\
							<div class="loaded"></div>\
						</div>\
					</div>\
					<div class="play-pause">\
						<span class="play control"></span>\
						<span class="pause control"></span>\
						<span class="loading control"></span>\
						<span class="error control"></span>\
					</div>\
					<div class="time">\
						<em class="played">00:00</em>/<strong class="duration">00:00</strong>\
					</div>\
					<div class="error-message"></div>\
				',
				playPauseClass: 'play-pause',
				scrubberClass: 'scrubber',
				progressClass: 'progress',
				loaderClass: 'loaded',
				timeClass: 'time',
				durationClass: 'duration',
				playedClass: 'played',
				errorMessageClass: 'error-message',
				playingClass: 'playing',
				loadingClass: 'loading',
				errorClass: 'error'
			}
		});
	});

})();
/* </script> */
