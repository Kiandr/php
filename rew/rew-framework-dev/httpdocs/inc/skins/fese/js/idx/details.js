/* global MAP_OPTIONS */
/* global IDX_LISTING */
(function () {

    // Listing info & geo co-ordinates
    var $details = $('#listing-details');
    var listing = $details.data('listing');
    var LISTING_LATITUDE = listing.geo && listing.geo[0];
    var LISTING_LONGITUDE = listing.geo && listing.geo[1];

    // Truncate listing remarks
    $details.find('.remarks').Truncate();

    // Listing pagination links
    var $paginate = $('#idx-paginate');
    if ($paginate.length === 1) {
        IDX.Paginate({
            mls: listing.mls,
            feed: listing.feed,
            done: function (data) {
                if (data.next && data.prev) $paginate.addClass('nextprev');
                if (data.next) $('<a class="next-listing" href="' + data.next + '">Next</a>').appendTo($paginate);
                if (data.prev) $('<a class="prev-listing" href="' + data.prev + '">Prev</a>').prependTo($paginate);
                if (data.next || data.prev) $paginate.removeClass('hidden');
            }
        });
    }

    // Save listing to favorites
    $details.on('click', 'a[data-save]', function (e) {
        var $this = $(this), data = $this.data('save');
        e.preventDefault();
        $details.Favorite({
            mls: data.mls,
            feed: data.feed,
            onComplete: function (response) {
                if (response.removed) $this.html(data.save);
                if (response.added) $this.html(data.remove);
            }
        });
    });

    // Load listing's map container
    var $map = $('#map-canvas');
    if (typeof MAP_OPTIONS === 'object') {
        $map.REWMap($.extend(true, {}, MAP_OPTIONS, {
            onInit: function () {

                // Setup Directions
                var $directions = $('#map-directions');
                if ($directions.length > 0) {
                    var $panel = $directions.find('.directions-panel');

                    // Directions Control
                    var directions = new REWMap.Directions({
                        renderer : {
                            map: this.getMap(), // Render on Map
                            panel: $panel.get(0) // Render to DOM Element
                        },
                        onSuccess: function () {
                            $panel.find('.msg.negative').remove();
                            $print.removeClass('hidden');
                        },
                        onFailure: function (error) {
                            $panel.html('<p class="msg negative">' + error + '</p>');
                            $print.addClass('hidden');
                        }
                    });

                    // Form Submit
                    var $form = $directions.on('submit', 'form', function (e) {
                        var from = $form.find('input[name="from"]').val(),
                            to = $form.find('input[name="to"]').val();
                        directions.getDirections(from, to);
                        e.preventDefault();
                    });

                    // Print Button
                    var $print = $('<a class="btn btn--primary hidden">Print Directions</a>').on('click', function () {
                        var w = window.open('about:blank');
                        w.document.write($panel.html());
                        w.document.close();
                        w.focus();
                        w.print();
                    }).appendTo($form.find('.btns'));

                }

            }
        }));
    }

    // Load Google Streetview
    var $streetview = $('#map-streetview'), streetview;
    if ($streetview.length === 1) {
        streetview = new REWMap.Streetview({
            el: $streetview.get(0),
            lat: LISTING_LATITUDE,
            lng: LISTING_LONGITUDE,
            onSuccess: function (data) {
                $details.find('.switch li[data-target="streetview"]').removeClass('hidden');
            }
        });
    }

    // Load MSVE Bird's Eye API
    var $birdseye = $('#map-birdseye'), birdseye;
    if (typeof MAP_OPTIONS === 'object') {
        $birdseye.REWMap($.extend(true, {}, MAP_OPTIONS, {
            type: 'satellite',
            init: false,
            zoom: 18
        }));
    }

    // Toggle map features
    $details.on('click', '.switch li', function () {
        var $link = $(this);
        var target = $link.data('target');
        var $target = $('#tab-' + target);

        // Toggle active tab content
        $target.removeClass('hidden')
            .siblings().addClass('hidden');

        // Toggle active tab link
        $('.switch li').removeClass('mnu-item--cur');
        $link.addClass('mnu-item--cur');
        // Close map tooltip
        var mapInstance = $map.REWMap('getSelf');
        if (typeof mapInstance === 'object') {
            mapInstance.getTooltip().hide(true);
        }

        switch (target) {

        // Map & Directions
        case 'map':
            $map.REWMap('show', function () {
                $map.REWMap('setCenter', LISTING_LATITUDE, LISTING_LONGITUDE);
            });
            break;

            // Google Streetview
        case 'streetview':
            if (streetview) {
                streetview.resize();
            }
            break;

            // Bird's Eye View
        case 'birdseye':
            $birdseye.REWMap('show', function () {
                $birdseye.REWMap('setCenter', LISTING_LATITUDE, LISTING_LONGITUDE);
            });
            break;

        }

    });

})();
