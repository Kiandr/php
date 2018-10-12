var audiojs = require('audiojs').audiojs;

(function () {
    // Load audio files
    var path = '/inc/js/vendor/audiojs/';
    audiojs.events.ready(function() {
        // We'll handle our own CSS thank you very much.
        audiojs.helpers.injectCss = function() {};

        var as = audiojs.createAll({
            imageLocation: path + 'player-graphics.gif',
            swfLocation: path + 'audiojs.swf',
            createPlayer: {
                markup: '\
					<div class="scrubber uk-position-relative">\
                        <div class="uk-progress uk-progress-striped uk-active uk-position-cover uk-border-rounded">\
                            <div class="progress uk-progress-bar uk-position-cover uk-position-z-index"></div>\
                            <div class="loaded uk-hidden"></div>\
                        </div>\
                        <div class="time uk-position-cover uk-text-center">\
						    <em class="played">00:00</em>/<strong class="duration">00:00</strong>\
					    </div>\
				    	<div class="play-pause uk-position-top-left">\
			    			<span class="play control uk-icon-play uk-cursor-pointer" title="Play"></span>\
		    				<span class="pause control uk-icon-pause uk-cursor-pointer" title="Pause"></span>\
	    					<span class="loading control uk-icon-cog uk-icon-spin" title="Loading"></span>\
    						<span class="error control uk-icon-warning uk-cursor-pointer"></span>\
					    </div>\
					</div>\
					<div class="error-message uk-alert uk-alert-danger uk-margin-remove"></div>\
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
            },
            css: false
        });
    });
})();
