var Cookie = require('../utils/cookie');

if (window.mapOptions && !REW.mapOptions) {
    // Handle the global options that search.php produces.
    REW.mapOptions = window.mapOptions;
}

var $form = $('.fw-idx-filter-container form');

// From rew.legacy
/**
 * Very useful function to format numbers.
 * Number.prototype.format (c, d, t)
 *  - c, count after decimal
 *  - d, decimal separater
 *  - t, thousands separater
 * Example: (10000).format(2, '.', ','); // 10,000.00
 */
Number.prototype.format = function (c, d, t) {
    var n = this, c = isNaN(c = Math.abs(c)) ? 0 : c, d = d == undefined ? '.' : d, t = t == undefined ? ',' : t, s = n < 0 ? '-' : '', i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + '', j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '');
};

REW.initBirdsEye = function ($container) {
    $container.REWMap({
        streetview: REW.mapOptions.streetview,
        birdseye: true,
        manager: REW.mapOptions.manager,
        center: REW.mapOptions.center,
        type: 'satellite',
        zoom: 19
    });
};

REW.initStreetView = function ($container, $tab) {
    $tab = typeof $tab !== 'undefined' ? $tab : false;
    new REWMap.Streetview({
        el: ($tab ? null : $container.get(0)),
        lat: REW.mapOptions.center.lat,
        lng: REW.mapOptions.center.lng,
        onFailure: function () {
            if (!$tab) {
                this.opts.el.innerHTML = '<p class="msg negative">We\'re sorry, but Google Streetview is currently unavailable for this property.</p>';
                this.opts.el.style.height = 'auto';
            }
        },
        onSuccess: function() {
            $('#streetview-tab').removeClass('uk-hidden');
        }
    });
};

REW.initDirectory = function ($container) {
    if ($container.data().hasOwnProperty('latitude') && $container.data().hasOwnProperty('longitude')) {
        $container.removeClass('uk-hidden');
        $container.REWMap({
            streetview: false,
            birdseye: false,
            manager: false,
            center: {'lat' : $container.data('latitude'), 'lng' : $container.data('longitude')},
            onInit : function () {
                var icon = new google.maps.MarkerImage('/img/map/marker-shopping@2x.png', null, null, null, new google.maps.Size(20, 25));
                var marker = new REWMap.Marker({
                    'map' : $container.data('REWMap'),
                    'icon' : icon,
                    'lat' : $container.data('latitude'),
                    'lng' : $container.data('longitude')
                });
            },
        });
    }else{
        console.error('Data-latitude/Data-longitude not set');
        return false;
    }
};

REW.initGoogleMap = function ($container) {

    var $legend = $('#idx-map-legend');

    // Search Query
    var search_query;
    var $searchMessage = $('#idx-map-message');
    var $searchTitle = $('#save-prompt');
    var $form_mapcontrols = $('#map-draw-controls');

    // Search in Bounds
    var eventDragEnd, eventZoomEnd, $searchBounds = $form_mapcontrols.find('input[name="map[bounds]"]'), forceBounds = false;
    $searchBounds.on({
        'click': function () {
            $(this).trigger('bounds');
        },
        'bounds': function () {
            var $this = $(this), checked = $this.prop('checked') ? true : false, $tooltip = $this.closest('.details').find('small');
            if (checked) {

                // Show Tooltip
                $tooltip.removeClass('hidden');

                // Google Map
                var gmap = $map.REWMap('getMap');

                // Bind Map Event (dragend)
                eventDragEnd = google.maps.event.addListener(gmap, 'dragend', function () {
                    $(REW).trigger('idx-submit', $form.serialize());
                });

                // Bind Map Event (zoomend)
                eventZoomEnd = google.maps.event.addListener(gmap, 'zoom_changed', function (oldLevel, newLevel) {
                    $(REW).trigger('idx-submit', $form.serialize());
                });

                // Trigger Search Request
                $(REW).trigger('idx-submit', $form.serialize());
            } else {

                // Hide Tooltip
                $tooltip.addClass('uk-hidden');

                // Unbind Map Event (dragend)
                if (eventDragEnd) google.maps.event.removeListener(eventDragEnd);

                // Unbind Map Event (zoomend)
                if (eventZoomEnd) google.maps.event.removeListener(eventZoomEnd);

            }

        }

    });
    // enable marker clustering
    $.extend(REW.mapOptions.manager, {cluster: true});
    REW.mapOptions = $.extend(REW.mapOptions, {
        init: REW.Helpers.isMapSearch(),
        onInit: function () {
            if (typeof $form === 'object') $form.trigger('submit');
            if (typeof $searchBounds === 'object') $searchBounds.trigger('bounds');
        },
        polygonControl: {
            onRefresh: function () {
                if (typeof $form === 'object') {
                    if (!this.hasSearches()) $form.find('input[name="map[polygon]"]').val('');
                    $form.trigger('toggleLocations');
                }
            },
            onDraw: function() {
                $(REW).trigger('idx-submit', $form.serialize());
                $(REW).trigger('idx-refresh');
            },
            onDelete: function() {
                $(REW).trigger('idx-submit', $form.serialize());
                $(REW).trigger('idx-refresh');
            }
        },
        radiusControl: {
            onRefresh: function () {
                if (typeof $form === 'object') {
                    if (!this.hasSearches()) $form.find('input[name="map[radius]"]').val('');
                    $form.trigger('toggleLocations');
                }
            },
            onDraw: function() {
                $(REW).trigger('idx-submit', $form.serialize());
                $(REW).trigger('idx-refresh');

            },
            onDelete: function() {
                $(REW).trigger('idx-submit', $form.serialize());
                $(REW).trigger('idx-refresh');
            }
        }
    });

    // Don't init if we're going to init on load
    var initialized = REW.mapOptions.init;
    var $map = window.$map = $container.REWMap(REW.mapOptions);

    // Map tools Map Settings
    $map.REWMap('setOptions', {
        'onInit' : function () {
            var $mapTools = $('#map-draw-controls'), radiusControl = this.radiusControl, radius = (radiusControl.hasSearches() ? radiusControl.getSearches()[0] : false),
                polygonControl = this.polygonControl, polygon = (polygonControl.hasSearches() ? polygonControl.getSearches()[0] : false);
            if ($mapTools.length) {
                var mapTools = $mapTools.removeClass('uk-hidden').get(0);
                this.gmap.controls[google.maps.ControlPosition.TOP_LEFT].push(mapTools);
                if (radius) this.gmap.fitBounds(radius.getBounds());
                if (polygon) {
                    var paths = polygon.getPaths();
                    var poly_bounds = new google.maps.LatLngBounds();
                    paths.forEach(function(path) {
                        var ar = path.getArray();
                        for(var i = 0, l = ar.length;i < l; i++) {
                            poly_bounds.extend(ar[i]);
                        }
                    });
                    this.gmap.fitBounds(poly_bounds);
                }
            }
            var $directions = $('#map-directions');
            if ($directions.length) {
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
                var $print = $('<a class="uk-button hidden">Print Directions</a>').on('click', function () {
                    var w = window.open('about:blank');
                    w.document.write($panel.html());
                    w.document.close();
                    w.focus();
                    w.print();
                }).appendTo($form.find('.btnset'));

            }
            // Trigger search
            $searchBounds.trigger('bounds');

        }
    });

    function initializeMap() {
        if (!initialized) {
            $map.REWMap('init');
        }
        $container.addClass('loaded');

        initialized = true;
    }

    $(document).on('click', '[data-toggle-map]', function () {
        var $this = $(this);
        $this.toggleClass('selected');
        Cookie('results-map', $this.hasClass('selected') ? 1 : null, {path: '/'});

        initializeMap();
        $('#map-draw-controls').toggleClass('uk-hidden', !$this.hasClass('selected'));
        $container.toggleClass('uk-hidden', !$this.hasClass('selected'));
    });

    if (!$container.hasClass('uk-hidden') || Cookie('results-map') || (criteria && criteria.map && criteria.map.open == 1) ) {
        $container.removeClass('uk-hidden');
        $('[data-toggle-map]').addClass('selected');
        initializeMap();
    }

    $(document).on('click', '.map-tab[data-tab]', function () {
        var $this = $(this);
        var $target = $($this.data('tab'));
        var closed = $legend.hasClass('closed');

        Cookie('idx-map-legend', !closed ? 'close' : 'open');

        function toggleHidden($target) {
            $target.removeClass('uk-hidden');
            $target.siblings().addClass('uk-hidden');
        }

        if (!$target.hasClass('uk-hidden') || closed) {
            // If the already open tab is clicked, close it all.
            var closeWidth = -($legend.width() - $legend.find('.map-tab').width());
            if (closed) {
                toggleHidden($target);
                $legend.css({right: closeWidth + 'px'}).removeClass('closed');
            }

            $legend.stop().animate({
                right: closed ? 0 : closeWidth
            }, 500, function () {
                if (!closed) {
                    $legend.addClass('closed');
                }
            });
        } else {
            toggleHidden($target);
        }
    }).on('change', '[name="map[type]"]', function () {
        $map.REWMap('setType', $(this).val());
    });

    $(document).on('click', '#field-map-submit', function() {
        var $this = $(this);
        if (!REW.Helpers.isMapSearch()) {
            $('.fw-idx-filter-container form').trigger('submit');
        }else{
            $(REW).trigger('idx-submit');
        }
    });

    var searches = 0;
    // Bind to Form Submit
    $(REW).on('idx-submit', function (e, searchArguments) {
        // Ignore events if we're not searching by map.
        if (!REW.Helpers.isMapSearch()) return;

        if ($form.length === 0) {
            //recall form, the var declare happened before the form was set
            $form = $('form.js-idx-search-header');
        }

        // Force Bounds on First Search (Reduces bouncing around of map when first loaded...)
        forceBounds = (searches === 0) ? true : false;

        // Increment
        searches++;

        // Prevent Default
        e.preventDefault();

        // Already Loading...
        if ($map.hasClass('loading')) return;

        // Clear Map
        $map.REWMap('clear');

        // Clear Map Layers
        if (layerManager) layerManager.clear();

        google.maps.event.addListener($map.REWMap('getMap'), 'idle', function() {

            // Show Map Loader
            $map.addClass('loading').REWMap('showLoader');

            // Map Data
            var center = $map.REWMap('getCenter'),
                zoom = $map.REWMap('getZoom'),
                bounds = $map.REWMap('getBounds'),
                polygons = $map.REWMap('getPolygons'),
                radiuses = $map.REWMap('getRadiuses'),
                checkbounds = $('input[type="checkbox"][name="map[bounds]"]:first').closest('form').triggerHandler('checkMap') === 'Bounds';

            // Map Criteria
            search_query = {
                // Map Data...
                'latitude': center.lat(),
                'longitude': center.lng(),
                'zoom': zoom,
                'ne': bounds.getNorthEast().toUrlValue(),
                'sw': bounds.getSouthWest().toUrlValue(),
                'polygon': (polygons ? polygons : ''),
                'radius': (radiuses ? radiuses : ''),
                // Form Fields...
                'sortorder': $('select[name="sortorder"]').val(),
                'bounds': (checkbounds || forceBounds) ? 1 : 0
            };

            // Update Form Data
            $form.find('input[name="map[latitude]"]').val(search_query.latitude);
            $form.find('input[name="map[longitude]"]').val(search_query.longitude);
            $form.find('input[name="map[ne]"]').val(search_query.ne);
            $form.find('input[name="map[sw]"]').val(search_query.sw);
            $form.find('input[name="map[polygon]"]').val(search_query.polygon);
            $form.find('input[name="map[radius]"]').val(search_query.radius);
            $form.find('input[name="map[zoom]"]').val(search_query.zoom);
            $form.find('input[name="sortorder"]').val(search_query.sortorder);

            // Disable Locations for First Search
            if (searches === 1) $form.find('.location').prop('disabled', true);

            // Search Criteria
            search_query = $.extend(search_query, {
                criteria: searchArguments,
                feed: REW.settings.idx.feed,
                search_url: REW.settings.urls.current + '?' + $form.serialize(),
                search_title: $form.find('input[name="search_title"]').val(),
                lead_id: $form.find('input[name="lead_id"]').val(),
                create_search: ($form.find('input[name="create_search"]').length == 1 ? true : false),
                edit_search: ($form.find('input[name="edit_search"]').length == 1 ? true : false),
            });

            // Reset Locations for First Search
            if (searches === 1) $form.find('.location').prop('disabled', false);

            // Save Title
            $searchTitle.html('"Loading Results..."');

            // Search Message
            $searchMessage.html('');

            // POST HTTP Request, Expect JSON
            $.ajax({
                'url': '/idx/inc/php/ajax/map.php?searchListings',
                'type': 'POST',
                'dataType': 'json',
                'data': search_query,
                'error': function () {

                    // Hide Loader
                    $map.removeClass('loading').REWMap('hideLoader');

                },
                'success': function (json) {

                    // Load Map Layers
                    $layers.filter(':checked').trigger('click').prop('checked', 'checked');

                    // Hide Loader
                    $map.removeClass('loading').REWMap('hideLoader');

                    // Successful Query
                    if (json.returnCode === 200) {

                        // Statistics
                        var stats = json.stats;
                        if (stats) {

                            // Update Statistics
                            $('#stats-total').html(stats.total);
                            $('#stats-price-avg').html(stats.price_avg);
                            $('#stats-price-high').html(stats.price_high);
                            $('#stats-price-low').html(stats.price_low);
                            $('#stats-sqft-avg').html(stats.sqft_avg);
                            $('#stats-sqft-high').html(stats.sqft_high);
                            $('#stats-sqft-low').html(stats.sqft_low);

                            // Show Statistics
                            if (Cookie('idx-map-legend') != 'close') {
                                $legend.find('.statsTrigger').removeClass('current').trigger('click');
                            }

                        }

                        // Search Title
                        if (json.search_title) $searchTitle.html('"' + json.search_title + '"');

                        // Results Found
                        var results = json.results, count = results && results.length;
                        if (results && count > 0) {

                            // Search Message
                            if (json.limit > 0) {
                                $searchMessage.html('<em>' + parseInt(json.count).format() + '</em> listings found, first <em>' + parseInt(count).format() + '</em> shown. Refine your search');
                            } else {
                                $searchMessage.html('Showing <em>' + parseInt(json.count).format() + '</em> Properties Found.');
                            }

                            // Fit Bounds If Not Searching Within Bounds
                            var fitBounds = ($searchBounds.is(':checked') !== true && !forceBounds) ? true : false;
                            $map.REWMap('getSelf').manager.opts.bounds = fitBounds;

                            // Load Markers
                            $map.REWMap('load', results);
                            $(REW).trigger('idx-refresh');

                            // No Results
                        } else {
                            $searchMessage.html('No results were found matching your search criteria.');

                        }

                        // Compliance Message
                        if (complianceLimit > 0) {
                            if (json.total > complianceLimit) {
                                $('#compliance-message').removeClass('hidden');
                            } else {
                                $('#compliance-message').addClass('hidden');
                            }
                        }

                        // Error Occurred
                    } else {
                        $searchMessage.html('An error has occurred. Please try your search again.');

                    }

                }
            });
        });
    });

    // Layer Icons
    var layerIcons = [];
    layerIcons['schools'] = ['/img/map/marker-school@2x.png', '/img/map/cluster-school@2x.png'];
    layerIcons['hospitals'] = ['/img/map/marker-hospital@2x.png', '/img/map/cluster-hospital@2x.png'];
    layerIcons['airports'] = ['/img/map/marker-airport@2x.png', '/img/map/cluster-airport@2x.png'];
    layerIcons['parks'] = ['/img/map/marker-park@2x.png', '/img/map/cluster-park@2x.png'];
    layerIcons['golf-courses'] = ['/img/map/marker-golf@2x.png', '/img/map/cluster-golf@2x.png'];
    layerIcons['churches'] = ['/img/map/marker-church@2x.png', '/img/map/cluster-church@2x.png'];
    layerIcons['shopping'] = ['/img/map/marker-shopping@2x.png', '/img/map/cluster-shopping@2x.png'];

    // Layers
    var $layers = $legend.find('input[name="map[layers][]"]'), layers = [], layerManager = null;

    // Toggle Layers
    $layers.on('click', function () {

        // Map Layer Type
        var $this = $(this),
            type = $this.val(),
            checked = $this.prop('checked'),
            title = (type.charAt(0).toUpperCase() + type.slice(1)).replace('-', ' ');

        // Un-Check Other Layers
        $layers.not($this).removeAttr('checked');

        // Update Layer Manager
        if (layerManager) {
            layerManager.clear();
            layerManager.icon.url = layerIcons[type][0];
            layerManager.iconCluster.url = layerIcons[type][1];
            layerManager.opts.titleStacked = '{x} ' + title + ' Found at This Location.';
            layerManager.opts.titleCluster = '{x} ' + title + ' Found.';
            $map.REWMap('getTooltip').hide(true);

            // Setup Layer Manager
        } else {
            layerManager = new REWMap.MarkerManager({
                map: $map.REWMap('getSelf'),
                icon: layerIcons[type][0],
                iconWidth: 20,
                iconHeight: 25,
                iconCluster: {
                    url: layerIcons[type][1],
                    labelOrigin: { x: 16, y: 11 },
                    scaledSize: { width: 31, height: 25 }
                },
                iconClusterWidth: 42,
                iconClusterHeight: 25,
                bounds: false,
                cluster: true,
                titleStacked: '{x} ' + title + ' Found at This Location.',
                titleCluster: '{x} ' + title + ' Found.'
            });

        }


        // Show Map Layers
        if (checked) {

            // Keep Checked
            $this.prop('checked', 'checked');

            // Random Process ID
            pid = Math.random() * 5;

            // Map Data
            var center = $map.REWMap('getCenter'),
                bounds = $map.REWMap('getBounds'),
                polygons = $map.REWMap('getPolygons'),
                radiuses = $map.REWMap('getRadiuses');

            // POST HTTP Request, Expect JSON
            $.ajax({
                'url': REW.settings.ajax.urls.map + '?searchLayers',
                'type': 'POST',
                'dataType': 'json',
                'data': {
                    'pid': pid,
                    'type': type,
                    'latitude': center.lat(),
                    'longitude': center.lng(),
                    'ne': bounds ? bounds.getNorthEast().toUrlValue() : null,
                    'sw': bounds ? bounds.getSouthWest().toUrlValue() : null,
                    'polygon': (polygons ? polygons : ''),
                    'radius': (radiuses ? radiuses : '')
                },
                'success': function (json, textStatus) {
                    if (!json || json.pid != pid) return;
                    if (json.returnCode == 200) {

                        // Load Results
                        layerManager.load(json.results);

                    }
                }
            });
        }
    });
};

$(function () {
    if (REW.mapOptions) {
        $.extend(true, REW.mapOptions, {
            canvas: '.fw-idx-map:first',
            directions: '#directions',
            directionsForm: '#directions'
        });
        window.mapOptions = REW.mapOptions;

        // Map search requires that advanced search by loaded
        REW.loadPanels(false);
    }
});
