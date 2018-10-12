/* <script> */
(function () {
    'use strict';

    // Shorten listing remarks
    $('#listing-body .remarks').Truncate();

    // Save to favorites
    $('#action-save[data-save]').on('click', function () {
        var $this = $(this)
            , data = $this.data('save')
  ;
        $('#listing-details').Favorite({
            mls: data.mls,
            feed: data.feed
        });
        return false;
    });

    // Dismiss this listing
    $('#action-hide[data-hide]').on('click', function () {
        var $this = $(this)
            , data = $this.data('hide')
  ;
        $('#listing-details').Dismiss({
            mls: data.mls,
            feed: data.feed
        });
        return false;
    });

    // Listing map container
    var $map = $('#map-canvas');

    // Listing tab switcher
    var $tabset = $('#listing-details .tabset').on('click', 'a[data-target]', function () {

        // Toggle tab content
        var $link = $(this), target = $link.data('target'), $target = $('#tab-' + target);
        $target.removeClass('hidden').siblings().addClass('hidden');
        $link.parent().addClass('current').siblings().removeClass('current');

        // Close map tooltip
        var mapInstance = $('#map-canvas').REWMap('getSelf');
        if (typeof mapInstance === 'object') {
            $map.REWMap('getTooltip').hide(true);
        }

        // Show map & directions
        if (target === 'map' && typeof $map === 'object') {

            // Show map container
            if (typeof mapInstance === 'object') {
                $map.REWMap('show', function () {
                    $map.REWMap('setCenter', IDX_LISTING.lat, IDX_LISTING.lng);
                });

            } else {

                // Load map instance
                $map.REWMap($.extend(true, MAP_OPTIONS, {
                    onInit: onMapLoad
                }));

            }

        }

        // Load google streetview
        if (target === 'streetview' && typeof streetview === 'object') {
            streetview.resize();
        }

        // Load bird's eye view on first click
        if (target === 'birdseye' && $birdseye.length > 0 && typeof birdseye !== 'object') {

            // Birds eye view
            birdseye = new REWMap($birdseye, $.extend(true, {}, MAP_OPTIONS, {
			    type: 'satellite',
			    zoom: 18
            }));

        }

    });

    // Load Bird's Eye View API
    var $birdseye = $('#map-birdseye')
        , birdseye
 ;

    // Listing data required
    if (typeof IDX_LISTING === 'object') {

        // Load Google Streetview
        var $streetview = $('#map-streetview');
        if ($streetview.length > 0) {
            var streetview = new REWMap.Streetview({
                el: $streetview.get(0),
                lat: IDX_LISTING.lat,
                lng: IDX_LISTING.lng,
                onSuccess : function (data) {
                    $tabset.find('a[data-target="streetview"]').parent().removeClass('hidden');
                }
            });
        }

        // Map initialization callback
        var onMapLoad = function () {

            // Update map center
            setTimeout(function () {
                this.setCenter(IDX_LISTING.lat, IDX_LISTING.lng);
            }.bind(this), 1);

            // Setup Directions
            var $displayDirections = $('#directions');
            if ($displayDirections.length > 0) {

                // Directions Control
                var directions = new REWMap.Directions({
                    renderer : {
                        map: this.getMap(), // Render on Map
                        panel: $displayDirections.get(0) // Render to DOM Element
                    },
                    onSuccess: function () {
                        $displayDirections.find('.msg.negative').remove();
                        $print.removeClass('hidden');
                    },
                    onFailure: function (error) {
                        $displayDirections.html('<p class="msg negative">' + error + '</p>');
                        $print.addClass('hidden');
                    }
                });

                // Form Submit
                var $form = $('#map-directions').on('submit', 'form', function () {
                    var from = $form.find('input[name="from"]').val(), to = $form.find('input[name="to"]').val();
                    directions.getDirections(from, to);
                    return false;
                });

                // Print Button
                var $print = $('<a class="hidden">Print Directions</a>').on('click', function () {
                    var w = window.open('about:blank');
                    w.document.write($displayDirections.html());
                    w.document.close();
                    w.focus();
                    w.print();
                }).insertBefore($displayDirections);

            }

        };

        // Listing pagination
        IDX.Paginate({
            mls: IDX_LISTING.mls,
            feed: IDX_LISTING.feed,
            done: function (data) {
                var $wrap = $('#listing-pagination');
                if (data.prev) $('<li><a class="prev-listing listing-toggle" href="' + data.prev + '">Prev</a></li>').appendTo($wrap);
                if (data.next) $('<li><a class="next-listing listing-toggle" href="' + data.next + '">Next</a></li>').appendTo($wrap);
            }
        });

    }

})();