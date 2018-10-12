(function () {
    'use strict';

    // Background container
    var $feature = $('#feature');
    var $background = $('#cover__background');

    // Slideshow
    var $slideshow = $('div[data-slideshow]');
    var $slides = $slideshow.find('.slide');
    var delay = 5000;

    if ($slides.length > 1) {
        $slides.not('.active').css('opacity', 0);
        var fnSlide = function (fw) {

            //Stop all active animations
            $slides.stop(true);

            //Get actual element
            var $actual = $slides.filter('.actual').eq(0);

            //If no actual element, use first active
            if(!$actual.size())
                $actual = $slides.filter('.active').eq(0);

            //Get next image depending on direction
            var $next = fw ? $actual.next().eq(0) : $actual.prev().eq(0);

            //Check end of list
            if (!$next.length) {
                $next = (fw ? $slides.first().eq(0) : $slides.last().eq(0));
            }

            //Fade in actual selected image
            $next.animate({ 'opacity' : 1 }, 1500).addClass('active').addClass('actual');

            //Fade out all other images
            $slides.not($next).animate({'opacity': 0}, 1500, function() { $slides.not($next).removeClass('active'); }).removeClass('actual');


        };

        //Add click function for slider controls
        $('.slider-controls .button').on('click', function (){
            //Stop Slide Interval
            clearInterval(slideInterval);

            //Run slider on direction clicked
            fnSlide(!$(this).hasClass('-left'));

            //Resume Slide Interval
            slideInterval = setInterval(fnSlide, delay, true);
        });

        //Start Slide Interval
        var slideInterval = setInterval(fnSlide, delay, true);
    }

    // YouTube Video
    var videoId = $feature.data('video-id');
    if (videoId && videoId.length > 0) {
        var videoMute = $feature.data('video-mute');
        var videoPlay = true;
        var videoPause = false;
        var videoPauseScroll = false;

        switch($feature.data('video-autoplay')) {
            case 'off':
                videoPlay = false;
                break;
            case 'on':
                videoPlay = true;
                break;
            case 'desktop':
                videoPlay = !isMobileDevice();
                break;
            case 'mobile':
                videoPlay = isMobileDevice();
                break;
            default:
                videoPlay = true;
        }

        switch($feature.data('video-autopause')) {
            case 'off':
                videoPause = false;
                break;
            case 'scroll':
                videoPauseScroll = true;
                videoPause = false;
                break;
            case 'view':
                videoPause = true;
                break;
            default:
                videoPause = false;
        }

        $background.YTPlayer({
            playerVars: {rel: 0, playsinline: 1, controls: 0, autoplay: videoPlay ? 1 : 0},
            fitToBackground: true,
            pauseOnScroll: !!videoPauseScroll,
            pauseOutOfView: !!videoPause,
            mute: !!videoMute,
            videoId: videoId
        });
    }

    // Panoramic Photo
    var panoImg = $feature.data('pano-src');
    if (panoImg && panoImg.length > 0) {
        var pano = $background.pano({
            img: panoImg,
            interval: 60,
            speed: 10,
            touches: 2
        });
        pano.moveLeft();
    }

    // 360 (VR) Photo
    var VRImg = $feature.data('vr-src');
    if (VRImg && VRImg.length > 0) {
        pannellum.viewer($feature.attr('id'), {
            type: 'equirectangular',
            panorama: VRImg,
            autoLoad: true,
            autoRotate: -2,
            mouseZoom: false,
            showControls: false,
            orientationOnByDefault: true,
            keyboardZoom: false,
            usedKeyNumbers: [16, 17, 27, 37, 38, 39, 40, 107, 109, 173, 187, 189],
            fingerTouches: 2,
            compass: false,
        });
    }

})();

/* search form */
var $inputC   = $('.input');
var $input  = $('.input input');
$input.on('focus', function() {
    $inputC.addClass('-isfocused');
});
$input.on('blur', function() {
   $inputC.removeClass('-isfocused');
});

$('input.autocomplete').Autocomplete();

/* IDLE TIMER */

var timeoutID;
function setup() {
    window.addEventListener('mousemove', resetTimer, false);
    window.addEventListener('mousedown', resetTimer, false);
    window.addEventListener('keypress', resetTimer, false);
    window.addEventListener('DOMMouseScroll', resetTimer, false);
    window.addEventListener('mousewheel', resetTimer, false);
    window.addEventListener('touchmove', resetTimer, false);
    window.addEventListener('MSPointerMove', resetTimer, false);
    startTimer();
}

setup();
 
function startTimer() {
    timeoutID = window.setTimeout(goInactive, 5000);
}
 
function resetTimer() {
    window.clearTimeout(timeoutID);
    goActive();
}
 
function goInactive() {
    $('#head').fadeOut(3000);
    setTimeout(function() {
        $('.cover__content').fadeOut(3000);
    }, 1000);
    // add cover over youtube to ensure header will display on mouse action
    $('#cover__background').addClass('ytp-timer-cover');
}
 
function goActive() {
    $('#head').fadeIn();
    $('.cover__content').fadeIn();
    $('#cover__background').removeClass('ytp-timer-cover');
    startTimer();
}

function isMobileDevice() {
    return (typeof window.orientation !== 'undefined') || (navigator.userAgent.indexOf('IEMobile') !== -1);
}

/**
 * DriveTime search component
 */
<?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_DRIVE_TIME'])) { ?>
(function() {

    var checkDrivetime = false;

    // Google Places Autocomplete
    REWMap.libraries.push('places');
    var checkDrivetime = false;
    REWMap.loadApi(function () {

        var $form = $('form#search_wrap'),
            $ac_input = $('.drivetime-ac-search'),
            $place_lat = $form.find('input[name="place_lat"]'),
            $place_lng = $form.find('input[name="place_lng"]'),
            autocomplete,
            input = $ac_input.get(0),
            driveTimeToolTip = document.querySelector('.drivetime-ac-search-tooltip');


        autocomplete = new google.maps.places.Autocomplete(input, {
            'types' : ['geocode'],
            'componentRestrictions' : {
                'country' : ($('html').attr('lang') === 'en-CA' ? 'CA' : 'US')
            }
        });

        // Resolves issue with un-clickable place options on mobile devices
        $(document).on({
            'DOMNodeInserted': function() {
                $('.pac-item, .pac-item span', this).addClass('needsclick');
            }
        }, '.pac-container');

        // Needed to prevent submit when using keys to select Google Places AC value(s)
        $ac_input.on('keydown', function (e) {
            if (e.keyCode === 13) {
                e.preventDefault();
            }
        });

        // Clear form fields if the Google Places AC input is emptied
        $ac_input.on('keyup', function () {
            if ($(this).val().length) return;
            // Drive Time Location Fields
            $form.find('input[name="place_zip"]').val('');
            $form.find('input[name="place_lat"]').val('');
            $form.find('input[name="place_lng"]').val('');
            $form.find('input[name="place_zoom"]').val('');
            $form.find('input[name="place_adr"]').val('');
        });

        // Show drivetime tooltip
        $ac_input.on('invalid', function (e) {
            e.preventDefault();
            this.classList.add('invalid');
            driveTimeToolTip.classList.remove('hidden');
        });

        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            driveTimeToolTip.classList.add('hidden');
            $ac_input.removeClass('invalid');
            var place = autocomplete.getPlace();
            if (typeof place !== 'undefined') {
                var includeZip = place.address_components[place.address_components.length - 1].long_name.length === 5
                    ? place.address_components[place.address_components.length - 1].long_name
                    : place.address_components[place.address_components.length - 2].long_name;

                // Update Hidden Inputs for Tracking Google Places Result Parts
                $form.find('input[name="place_zip"]').val(includeZip);
                $place_lat.val(place.geometry.location.lat());
                $place_lng.val(place.geometry.location.lng());
                $form.find('input[name="place_adr"]').val(place.formatted_address);
            }

            if ($ac_input.val() === '') return;
        });

        $form.on('submit', function (e) {

            place_valid = ($place_lat.val().length > 0 && $place_lng.val().length > 0);
            if(checkDrivetime && !place_valid) {
                e.preventDefault();
                $ac_input.trigger('invalid');
            }
            // updating the zoom input based on the selected travel duration
            var dt_duration = $form.find('select[name="dt_travel_duration"]').val(),
                dt_zoom = 13;

            if (dt_duration >= 75) {
                dt_zoom = 8;
            } else if (dt_duration >= 45) {
                dt_zoom = 9;
            } else if (dt_duration >= 30) {
                dt_zoom = 10;
            } else if (dt_duration >= 15) {
                dt_zoom = 12;
            }

            $form.find('input[name="place_zoom"]').val(dt_zoom);
        });
    });

    // Toggle between default and drivetime search
    var searchWrap = document.getElementById('container_search');
    var defaultSearch = searchWrap.querySelector('[data-action="search_default"]');
    var drivetimeSearch = searchWrap.querySelector('[data-action="search_drivetime"]');
    var dtDirection = searchWrap.querySelectorAll('input[name="dt_direction"]');
    var dtAddress = searchWrap.querySelector('input[name="dt_address"]');
    var dtArrivalTime = searchWrap.querySelector('select[name="dt_arrival_time"]');
    var dtTravelDuration = searchWrap.querySelector('select[name="dt_travel_duration"]');
    var placeZip = searchWrap.querySelector('input[name="place_zip"]');
    var placeLat = searchWrap.querySelector('input[name="place_lat"]');
    var placeLng = searchWrap.querySelector('input[name="place_lng"]');
    var placeZoom = searchWrap.querySelector('input[name="place_zoom"]');
    var placeAdr = searchWrap.querySelector('input[name="place_adr"]');
    var searchLocation = searchWrap.querySelector('input[name="search_location"]');

    if (searchWrap) {
        searchWrap.addEventListener('click', function(evt) {
            var toggleInitiated = evt.target === defaultSearch || evt.target === drivetimeSearch;
            if (toggleInitiated) {
                toggleActiveOption('search_options', evt);
                toggleActiveSearch('search_wrap', evt);
            }
            if (evt.target === defaultSearch) {
                // Enable Options
                searchLocation.removeAttribute('disabled');
                // Disable Options
                for (var i = 0; i < dtDirection.length; i++) {
                    dtDirection[i].setAttribute('disabled', '');
                }
                dtAddress.setAttribute('disabled', '');
                dtArrivalTime.setAttribute('disabled', '');
                dtTravelDuration.setAttribute('disabled', '');
                placeZip.setAttribute('disabled', '');
                placeLat.setAttribute('disabled', '');
                placeLng.setAttribute('disabled', '');
                placeZoom.setAttribute('disabled', '');
                placeAdr.setAttribute('disabled', '');
            }
            if (evt.target === drivetimeSearch) {
                // Enable Options
                for (var i = 0; i < dtDirection.length; i++) {
                    dtDirection[i].removeAttribute('disabled');
                }
                dtAddress.removeAttribute('disabled');
                dtArrivalTime.removeAttribute('disabled');
                dtTravelDuration.removeAttribute('disabled');
                placeZip.removeAttribute('disabled');
                placeLat.removeAttribute('disabled');
                placeLng.removeAttribute('disabled');
                placeZoom.removeAttribute('disabled');
                placeAdr.removeAttribute('disabled');
                // Disable Options
                searchLocation.setAttribute('disabled', '');
            }
        });
    }

    /**
     * Toggle search options
     * @param {string} parentId - The parent element `id` attribute
     * @param {object} evt - The `Event` object
     */

    function toggleActiveOption(parentId, evt) {
        var searchOptions = document.getElementById(parentId).children;
        var searchOptionsArr = Array.prototype.slice.call(searchOptions);
        for (var i = 0; i < searchOptionsArr.length; i++) {
            var toggleInitiated = searchOptionsArr[i] === evt.target;
            if (toggleInitiated && searchOptionsArr[i].dataset.active !== 'true') {
                searchOptionsArr[i].setAttribute('data-active', 'true');
            }
            if (!toggleInitiated) searchOptionsArr[i].setAttribute('data-active', 'false');
        }
    }

    /**
     * Toggle search components
     * @param {string} parentId - The parent element `id` attribute
     * @param {object} evt - The `Event` object
     */

    function toggleActiveSearch(parentId, evt) {
        checkDrivetime = !checkDrivetime;
        var searchComponents = document.getElementById(parentId).children;
        var searchComponentsArr = Array.prototype.slice.call(searchComponents);
        for (var i = 0; i < searchComponentsArr.length; i++) {
            var toggleInitiated = searchComponentsArr[i].id === evt.target.dataset.action;
            if (toggleInitiated && searchComponentsArr[i].dataset.active !== 'true') {
                searchComponentsArr[i].setAttribute('data-active', 'true');
            }
            if (!toggleInitiated) searchComponentsArr[i].setAttribute('data-active', 'false');
        }
    }
})()
<?php } ?>