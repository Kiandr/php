/* global MAP_OPTIONS */
/* global IDX_LISTING */
(function () {

    // Listing info & geo co-ordinates
    var $details = $('#listing-details');
    var listing = $details.data('listing');
    var LISTING_LATITUDE = listing.geo && listing.geo[0];
    var LISTING_LONGITUDE = listing.geo && listing.geo[1];

    // Save listing to favorites
    var $save = $details.find('a[data-save]');
    $save.on('click', function (e) {
        var $this = $(this), data = $this.data('save');
        e.preventDefault();
        if (!data.div) {
            $details.Favorite({
                mls: data.mls,
                feed: data.feed,
                onComplete: function (response) {
                    if (response.added) $this.addClass('saved').find('span').html(data.remove);
                    if (response.removed) $this.removeClass('saved').find('span').html(data.save);
                }
            });
        }
    });

    // Listing pagination links
    var $paginate = $('#idx-paginate');
    if ($paginate.length === 1) {
        IDX.Paginate({
            mls: listing.mls,
            feed: listing.feed,
            done: function (data) {
                if (data.next && data.prev) $paginate.addClass('nextprev');
                if (data.next) $('<a class="button button--bordered column -width-1/2 next-listing" href="' + data.next + '"><svg class="icon icon--xs -block -center -mar-vertical-xs"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/inc/skins/ce/img/assets.svg#icon--right-arrow"></use></svg><span>Next Result</span></a>').appendTo($paginate);
                if (data.prev) $('<a class="button button--bordered column -width-1/2 prev-listing" href="' + data.prev + '"><svg class="icon icon--xs -block -center -mar-vertical-xs"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/inc/skins/ce/img/assets.svg#icon--left-arrow"></use></svg><span>Previous Result</span></a>').prependTo($paginate);
                if (data.next || data.prev) $paginate.removeClass('hidden');
            }
        });
    }

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
                            $panel.find('.notice--negative').remove();
                            $print.removeClass('hidden');
                        },
                        onFailure: function (error) {
                            $panel.html('<p class="notice--negative">' + error + '</p>');
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
                    var $print = $('<a class="button -text-sm -mar-bottom-xs print-directions hidden">Print Directions</a>').on('click', function () {
                        var w = window.open('about:blank');
                        w.document.write($panel.html());
                        w.document.close();
                        w.focus();
                        w.print();
                    }).appendTo($form.find('.buttons'));

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
                $details.find('a[data-target="streetview"]').parent().removeClass('hidden');
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
    $details.on('click', 'a[data-target]', function () {
        var $link = $(this);
        var target = $link.data('target');
        var $target = $('#tab-' + target);

        // Toggle active tab content
        $target.removeClass('hidden')
            .siblings().addClass('hidden');

        // Toggle active tab link
        $link.parent().addClass('nav__item -is-current')
            .siblings().removeClass('-is-current');

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
