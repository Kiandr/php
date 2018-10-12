require('../idx/mapping');
var Cookie = require('./cookie');

// Binds all the things.
global.REW.Bind = function ($el) {
    // Make sure we don't double bind
    if ($el.hasClass('bound')) return;
    $el.addClass('bound');

    // Auto defer, [data-src] = legacy BREW defer, ignore builder images
    global.REW.Defer($el.find('.deferred, [data-src]').not($('.item-image img[data-src], .bdx-properties-photo img[data-src]')));

    // Bind slideshows
    $el.find('.fw-slider').each(function () {
        var $this = $(this);

        var config = $this.data('fw-slider-config') || {};

        // Extract config that doesn't belong to Slick
        var deferred = config.deferred || false;
        delete config.deferred;

        if (deferred) {
            REW.Defer($this.find('[data-fw-deferred-img-config]'), function () {
                // Deferred load completed! Lets slideshow it up!
                REW.Slideshow($this, config);
            });

        } else {
            REW.Slideshow($this, config);
        }
    });

    $el.find('[data-display-photos]').on('click', function () {
        var $this = $(this);

        IDX.DisplayPhotos({
            require_registration: !!$this.data('register')
        }, $('.photos-popup'));
    });

    // Init google map
    var $gmap = $el.find('.fw-idx-map:first');
    if ($gmap.length) {
        REW.initGoogleMap($gmap);
    }

    // Init birdseye
    var $bmap = $el.find('#birdseye-container');
    if ($bmap.length) {
        REW.initBirdsEye($bmap);
    }

    var $smap = $el.find('#streetview-container');
    if ($smap.length) {
        REW.initStreetView($smap);
    }

    var $s_tabmap = $el.find('#streetview-tab');
    if ($s_tabmap.length) {
        REW.initStreetView($s_tabmap, true);
    }

    var $directorymap = $el.find('#directory-map');
    if ($directorymap.length) {
        REW.initDirectory($directorymap);
    }

    if (Cookie('display-photos-if-registered')) {
        var $link = $el.find('[data-display-photos]');
        if ($link.length && !$link.data('register')) {
            IDX.DisplayPhotos({
                require_registration: false
            }, $('.photos-popup'));
        }
        Cookie('display-photos-if-registered', null);
    }
    var $truncate_text = $el.find('.truncate');
    if ($truncate_text.length) {
        $truncate_text.each(function(){
            REW.Helpers.truncate(this, $(this).data('truncate'));
        });

    }
};

$(document).ready(function () {
    // Bind dialog links
    REW.DialogLinks();

    // Bind OAuth links
    REW.OAuth();

    // Bindings that need to run individually for new content
    REW.Bind($(document));
});
