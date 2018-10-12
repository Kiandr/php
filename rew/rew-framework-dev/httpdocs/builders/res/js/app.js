// Require Config
requirejs.config({
    baseUrl: '/builders/res/js/',
    waitSeconds : 200,
    paths : {
        thebdx			: ['//resources.newhomesource.com/GlobalResourcesMvc/Default/js/BDXTracker'],
        nouislider  	: ['lib/jquery.nouislider'],
        carousel		: ['lib/jquery.flexslider'],
        eqheight		: ['lib/jquery.eqheight'],
        brew			: ['lib/brew'],
        util			: ['lib/util'],
        states 			: ['states'],
        cities 			: ['cities'],
        search 			: ['search'],
        community 		: ['community'],
        listing 		: ['listing'],
    },
});

$(function() {

    // BDX Tracking
    require(['thebdx'], function () {
        var _tracker = new BDX.Scripts.Tracker({partnerId: '8910'});
        sendTrackerData = function () {
            if (typeof _trackerData === 'object') {
                for (event in _trackerData) {
                    if (_trackerData.hasOwnProperty(event)) {
                        var d = _trackerData[event], l = d.length, i = 0;
                        for (i; i < l; i++) _tracker.logEvent(event, d[i]);
                    }
                }
            }
        };
        sendTrackerData();
        // Track Clickthroughs
        $('#body a[data-tracker]').click(function (e) {
            e.preventDefault();
            tracker = $(this).data('tracker');
            _tracker.logEvent(tracker[0], tracker[1]);
            document.location = $(this).attr('href');
            return true;
        });
    });

    require(['eqheight', 'brew'], function () {
        // adding brew image resizing and eqHeight for any grids
        $('.item-image').not('.bdx-state-item .item-image, .bdx-city-item .item-image').Images({
            resize : {
                method : 'scale'
            },
            onComplete: function() {
                $('.bdx-listings-grid').eqHeight();
            }
        });

        $('.bdx-state-item .item-image, .bdx-city-item .item-image').Images({
            resize : {
                method : 'crop',
                ratio : '1:1'
            },
            onComplete: function() {
                $('.bdx-listings-grid').eqHeight();
            }
        });

        $('.bdx-agent-photo, .bdx-lender-photo, .bdx-properties-photo').Images({
            resize : {
                method : 'crop',
                ratio : '1:1'
            }
        });


        // Timeout...
        setTimeout(function () {
            $('.bdx-listings-grid').eqHeight();
        }, 0);

        // Check Window Resize
        $(window).bind('smartresize', function () {
            $('.bdx-listings-grid').children().css('min-height', 'auto');
            $('.bdx-listings-grid').eqHeight();
        });
    });
});


// smooth scrolling
$('a.scroll-to[href*=#]:not([href=#])').on('click', function (e) {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
        var target = $(this.hash);
        // need to factor in current header height so doesn't cover linked content
        target = target.length ? target : $('#' + this.hash.slice(1));
        if (target.length) {
            $('html,body').animate({
                scrollTop: target.offset().top
            }, 1100);
            return false;
        }
    }
});
