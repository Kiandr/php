// IDX map search
var $form = $('form.idx-search'), $searchBounds = $form.find('input[name="map[bounds]"]:checkbox');
(function () {
    'use strict';

    // Map tools Map Settings
    $map.REWMap('setOptions', {
        onInit: function () {
            var $mapTools = $('#map-draw-controls');
            if ($mapTools.length > 0) {
                var mapTools = $mapTools.removeClass('hidden').get(0);
                this.gmap.controls[google.maps.ControlPosition.TOP_LEFT].push(mapTools);
            }
            // Trigger search
            $form.trigger('submit');
            $searchBounds.trigger('bounds');
        }
    });

    var searches = 0, pid;

    // Toggle results sort order (ASC/DESC)
    var $toolbar = $('#search-toolbar');
    var $sortorder = $form.find('input[name="sortorder"]');

    // Search in Bounds
    var eventDragEnd, eventZoomEnd, forceBounds = false;
    $searchBounds.on({
        click: function () {
            $(this).trigger('bounds');
        },
        bounds: function () {
            var $this = $(this), checked = $this.attr('checked') ? true : false, $tooltip = $this.closest('.details').find('small');
            if (checked) {

                // Show Tooltip
                $tooltip.removeClass('hidden');

                // Google Map
                var gmap = $map.REWMap('getMap');

                // Bind Map Event (dragend)
                eventDragEnd = google.maps.event.addListener(gmap, 'dragend', function() {
                    $form.trigger('submit');
                });

                // Bind Map Event (zoomend)
                eventZoomEnd = google.maps.event.addListener(gmap, 'zoom_changed', function(oldLevel, newLevel) {
                    $form.trigger('submit');
                });

                // Trigger Search Request
                $form.trigger('submit');

            } else {

                // Hide Tooltip
                $tooltip.addClass('hidden');

                // Unbind Map Event (dragend)
                if (eventDragEnd) google.maps.event.removeListener(eventDragEnd);

                // Unbind Map Event (zoomend)
                if (eventZoomEnd) google.maps.event.removeListener(eventZoomEnd);

            }

        }

    });

    // Map Legend
    var $legend = $('#idx-map-legend');

    // Toggle Map Legend
    $legend.on('toggleLegend', function () {
        if ($legend.hasClass('closed')) {
            $legend.stop().animate({
                'right' : 0
            }, 500).removeClass('closed');
            BREW.Cookie('idx-map-legend', 'open');
        } else {
            $legend.stop().animate({
                'right' : $legend.find('.legend_tab').width() - $legend.width()
            }, 500).addClass('closed');
            BREW.Cookie('idx-map-legend', 'close');
        }
    });

    // Position Map Legend
    $legend.show().css({
        'position' : 'absolute',
        'right' : $legend.find('.legend_tab').width() - $legend.width(),
        'top' : 75
    });

    // Show Options
    $legend.on(BREW.events.click, '.legendTrigger', function() {
        var $this = $(this);
        $('.legend_contents').removeClass('hidden').siblings().addClass('hidden');
        if ($this.hasClass('current') || $legend.hasClass('closed')) $legend.trigger('toggleLegend');
        $this.addClass('current').siblings().removeClass('current');
    });

    // Show Stats
    $legend.on(BREW.events.click, '.statsTrigger', function() {
        var $this = $(this);
        $('.stats_contents').removeClass('hidden').siblings().addClass('hidden');
        if ($this.hasClass('current') || $legend.hasClass('closed')) $legend.trigger('toggleLegend');
        $this.addClass('current').siblings().removeClass('current');
    });

    // Switch Map Type
    $legend.on('change', 'select[name="map[type]"]', function () {
        var $this = $(this).blur(), type = $this.val();
        $map.REWMap('setType', type);
    });

    // Search Query
    var search_query, $searchMessage = $('#idx-map-message'), $searchTitle = $('#save-prompt');

    // Bind to Form Submit
    $form.on('submit', function (e) {

        // Saved search id
        var $saved_search_id = $form.find('input[name="saved_search_id"]')
            , saved_search_id = $saved_search_id ? $saved_search_id.val() : 0
            ;

        // Force Bounds on First Search (Reduces bouncing around of map when first loaded...)
        forceBounds = (searches === 0 && saved_search_id < 1) ? true : false;

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

        // Show Map Loader
        $map.addClass('loading').REWMap('showLoader');

        // Map Data
        var center = $map.REWMap('getCenter'),
            zoom = $map.REWMap('getZoom'),
            bounds = $map.REWMap('getBounds'),
            polygons = $map.REWMap('getPolygons'),
            radiuses = $map.REWMap('getRadiuses');

        // Map Criteria
        search_query = {
            // Map Data...
            'latitude'	: center.lat(),
            'longitude'	: center.lng(),
            'zoom'		: zoom,
            'ne'		: bounds.getNorthEast().toUrlValue(),
            'sw'		: bounds.getSouthWest().toUrlValue(),
            'polygon'	: (polygons ? polygons : ''),
            'radius'	: (radiuses ? radiuses : ''),
            // Form Fields...
            'sortorder' : $sortorder.val(),
            'bounds'	: ($searchBounds.attr('checked') || forceBounds) ? 1 : 0
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
        if (forceBounds) $form.find('.location').prop('disabled', true);

        // Search Criteria
        search_query = $.extend(search_query, {
            criteria		: $form.serialize(),
            feed			: $form.find(':input[name="idx"]').val(),
            search_url		: '?' + $form.serialize(),
            search_title	: $form.find('input[name="search_title"]').val(),
            lead_id			: $form.find('input[name="lead_id"]').val(),
            create_search	: ($form.find('input[name="create_search"]').length == 1 ? true : false),
            edit_search		: ($form.find('input[name="edit_search"]').length == 1 ? true : false)
        });

        // Reset Locations for First Search
        if (forceBounds) $form.find('.location').prop('disabled', false);

        // Save Title
        $searchTitle.html('"Loading Results..."');

        // Search Message
        $searchMessage.html('');

        // POST HTTP Request, Expect JSON
        $.ajax({
            'url'		: '/idx/inc/php/ajax/map.php?searchListings',
            'type'		: 'POST',
            'dataType'	: 'json',
            'data'		: search_query,
            'error'		: function () {

                // Hide Loader
                $map.removeClass('loading').REWMap('hideLoader');

            },
            'success'	: function (json) {

                // Load Map Layers
                $layers.filter(':checked').trigger(BREW.events.click).attr('checked', 'checked');

                // Hide Loader
                $map.removeClass('loading').REWMap('hideLoader');

                // Successful Query
                if (json.returnCode === 200) {

                    // Create Drive Time Polygon
                    if (json.polygon_data.length) {
                        var response_polys = json.polygon_data.split(','),
                            dt_polys = [];
                        for (var i=0; i<response_polys.length; i++) {
                            var dt_coordinates = response_polys[i].trim().split(' ');
                            dt_polys.push({
                                lat: dt_coordinates[0].replace('[', '').replace('"', ''),
                                lng: dt_coordinates[1].replace(']', '').replace('"', ''),
                            });
                        }
                        $map.REWMap('getSelf').polygonControl.clear();
                        $map.REWMap('getSelf').polygonControl.load([dt_polys]);
                    }

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
                        if (BREW.Cookie('idx-map-legend') != 'close') {
                            $legend.find('.statsTrigger').removeClass('current').trigger(BREW.events.click);
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

    // Layer Icons
    var layerIcons = [];
    layerIcons['schools']		= ['/img/map/marker-school@2x.png',		'/img/map/cluster-school@2x.png'];
    layerIcons['hospitals']		= ['/img/map/marker-hospital@2x.png',	'/img/map/cluster-hospital@2x.png'];
    layerIcons['airports']		= ['/img/map/marker-airport@2x.png',	'/img/map/cluster-airport@2x.png'];
    layerIcons['parks']			= ['/img/map/marker-park@2x.png',		'/img/map/cluster-park@2x.png'];
    layerIcons['golf-courses']	= ['/img/map/marker-golf@2x.png',		'/img/map/cluster-golf@2x.png'];
    layerIcons['churches']		= ['/img/map/marker-church@2x.png',		'/img/map/cluster-church@2x.png'];
    layerIcons['shopping']		= ['/img/map/marker-shopping@2x.png',	'/img/map/cluster-shopping@2x.png'];

    // Layers
    var $layers = $legend.find('input[name="map[layers][]"]'), layers = [], layerManager = null;

    // Toggle Layers
    $layers.on(BREW.events.click, function () {

        // Map Layer Type
        var $this = $(this),
            type = $this.val(),
            checked = $this.attr('checked'),
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
                map				: $map.REWMap('getSelf'),
                icon			: layerIcons[type][0],
                iconWidth		: 20,
                iconHeight		: 25,
                iconCluster: {
                    url: layerIcons[type][1],
                    labelOrigin: { x: 16, y: 11 },
                    scaledSize: { width: 31, height: 25 }
                },
                iconClusterWidth	: 42,
                iconClusterHeight	: 25,
                bounds			: false,
                cluster			: true,
                titleStacked	: '{x} ' + title + ' Found at This Location.',
                titleCluster	: '{x} ' + title + ' Found.'
            });

        }


        // Show Map Layers
        if (checked) {

            // Keep Checked
            $this.attr('checked', 'checked');

            // Random Process ID
            pid = Math.random() * 5;

            // Map Data
            var center = $map.REWMap('getCenter'),
                bounds = $map.REWMap('getBounds'),
                polygons = $map.REWMap('getPolygons'),
                radiuses = $map.REWMap('getRadiuses');

            // POST HTTP Request, Expect JSON
            $.ajax({
                'url' : '/idx/inc/php/ajax/map.php?searchLayers',
                'type' : 'POST',
                'dataType' : 'json',
                'data' : {
                    'pid'		: pid,
                    'type'		: type,
                    'latitude'	: center.lat(),
                    'longitude'	: center.lng(),
                    'ne'		: bounds.getNorthEast().toUrlValue(),
                    'sw'		: bounds.getSouthWest().toUrlValue(),
                    'polygon'	: (polygons ? polygons : ''),
                    'radius'	: (radiuses ? radiuses : '')
                },
                'success'	: function (json, textStatus) {
                    if (!json || json.pid != pid) return;
                    if (json.returnCode == 200) {

                        // Load Results
                        layerManager.load(json.results);

                    }
                }
            });

        }

    });

    // Save This Search
    $('#save-search').live(BREW.events.click, function () {
        var search = search_query;
        search.search_by = 'map';
        saveSearch(search);
        return false;
    });

    // Edit Search
    $('#edit-search, #edit-search-email').live(BREW.events.click, function () {

        // Trigger search update
        $form.trigger('submit');

        // Search Data
        var search = search_query;
        search.search_by = 'map';
        search.saved_search_id = $form.find('input[name="saved_search_id"]').val();
        search.search_title = $form.find('input[name="search_title"]').val();
        search.frequency = $form.find('select[name="frequency"]').val();
        search.email_results_immediately = $(this).attr('id') == 'edit-search-email' ? 'true' : 'false';

        // Edit Search
        editSearch(search);

        // Return
        return false;

    });

})();