// Search Form
var $form = $('form.idx-search');
(function () {
    'use strict';

    // Over-ride map options and add drawing tools
    $map.REWMap('setOptions', $.extend(true, mapOptions || {}, {
        onInit: function () {
            var $mapTools = $('#map-draw-controls');
            if ($mapTools.length > 0) {
                var mapTools = $mapTools.removeClass('hidden').get(0);
                this.gmap.controls[google.maps.ControlPosition.TOP_LEFT].push(mapTools);
                $mapTools.on('click', '.refine-btn', function () {
                    $form.trigger('submit');
                });
            }
        }
    }));

    // Search Toolbar
    var $toolbar = $('.toolbar:not(.rt-toolbar)');

    // Switch View
    $toolbar.on(BREW.events.click, 'a.view', function (e) {
        var $this = $(this), view = $(this).attr('href');
        var isHuman = e.originalEvent !== undefined;

        // Grid
        if (view == '#grid') {
            BREW.Cookie('results-view', state.view = 'grid');
            $this.parent().addClass('current').siblings().removeClass('current');
            $('#content .listings:not(.rt-listings)').removeClass('colset-1 layout-detailed').addClass('colset-1-sm colset-2-md colset-3-lg colset-3-xl').eqHeight();
            return true;

            // List
        } else if (view == '#detailed') {
            BREW.Cookie('results-view', state.view = 'detailed');
            $this.parent().addClass('current').siblings().removeClass('current');
            $('#content .listings:not(.rt-listings)').removeClass('colset-1-sm colset-2-md colset-3-lg colset-3-xl').addClass('colset-1 layout-detailed').find('.listing').css('min-height', 0);
            return true;

            // Map
        } else if (view == '#map') {

            // Show Map
            if ($map.hasClass('hidden')) {

                state.map = 1;

                // Show Map
                $map.hide().REWMap('show', function () {
                    $('#field-polygon').removeClass('hidden');
                    $('#field-radius').removeClass('hidden');
                    $('#field-bounds').removeClass('hidden');
                });

                //Add body class for map specific template adaptations
                $('body').addClass('map-displayed');

            } else {

                state.map = 0;

                // Hide Map
                $map.REWMap('hide', function () {
                    $('#field-polygon').addClass('hidden');
                    $('#field-radius').addClass('hidden');
                    $('#field-bounds').addClass('hidden');
                });

                // Remove body class
                $('body').removeClass('map-displayed');

            }
            // Save State
            if (isHuman) BREW.Cookie('results-map', state.map);

            return false;
        }
    });
	
    //View toggle removes office and disclaimer when compliance rule met
    window.onload = function() {
        if(document.getElementById('gridViewOffice')) {

            var g = document.getElementById('gridViewOffice');
            var d = document.getElementById('detailedViewOffice');

			  if(!$('.layout-detailed')[0] || $('.flowgrid')[0]) {
				  $('.office').addClass('hidden');
			 }

            g.addEventListener('click', gridView, false);
            d.addEventListener('click', detailedView, false);

            function gridView() {
                $('.office').addClass('hidden');
            }

            function detailedView() {
                $('.office').removeClass('hidden');
            }
        }

        if(document.getElementById('gridView')) {

            var g = document.getElementById('gridView');
            var d = document.getElementById('detailedView');

			  if(!$('.layout-detailed')[0] || $('.flowgrid')[0]) {
				  $('.office').addClass('hidden');
				  $('.mls-disclaimer').addClass('hidden');
			 }

			 g.addEventListener('click', gridView, false);
			 d.addEventListener('click', detailedView, false);

            function gridView() {
                $('.office').addClass('hidden');
                $('.mls-disclaimer').addClass('hidden');
            }

            function detailedView() {
                $('.office').removeClass('hidden');
                $('.mls-disclaimer').removeClass('hidden');
            }
		  }
    };	
	
    // Show Map
    var show_map = (criteria && criteria.map && criteria.map.open == 1) ? true : false;

    // Results State
    var state = {
        view : window.location.hash.indexOf('#') != -1 ? window.location.hash.substr(window.location.hash.indexOf('#') + 1) : view,
        map : (show_map || BREW.Cookie('results-map') == 1) && BREW.Cookie('results-map') != 0 ? true : false
    };

    // Trigger View
    if (state.view) $toolbar.find('a.view[href="#' + state.view + '"]').trigger(BREW.events.click);

    // Show Map
    if (!BREW.mobile && state.map && state.view != 'map') $toolbar.find('a.view[href="#map"]').trigger(BREW.events.click);

    // Bind to Form Submit
    $form.bind('submit', function (e) {

	    // Save Map Details
	    var center = $map.REWMap('getCenter');
	    if (center) {
	        $form.find('input[name="map[latitude]"]').val(center.lat());
	    	$form.find('input[name="map[longitude]"]').val(center.lng());
	    }

	    // Zoom Level
	    var zoom = $map.REWMap('getZoom');
	    if (zoom) {
	    	$form.find('input[name="map[zoom]"]').val(zoom);
	    }

	    // Map Bounds
	    var bounds = $map.REWMap('getBounds');
        $form.find('input[name="map[ne]"]').val(bounds ? bounds.getNorthEast().toUrlValue() : '');
        $form.find('input[name="map[sw]"]').val(bounds ? bounds.getSouthWest().toUrlValue() : '');
        $form.find('input[name="map[bounds]"]').val($form.triggerHandler('checkMap') === 'Bounds' ? 1 : 0);

	    // Polygon Searches
	    var polygons = $map.REWMap('getPolygons'), $polygons = $form.find('input[name="map[polygon]"]');
	    if (typeof polygons !== 'undefined') $polygons.val(polygons ? polygons : '');

	    // Radius Searches
	    var radiuses = $map.REWMap('getRadiuses'), $radiuses = $form.find('input[name="map[radius]"]');
	    if (typeof radiuses !== 'undefined') $radiuses.val(radiuses ? radiuses : '');

    });

    // Save This Search
    $('#save-link').live(BREW.events.click, function () {
        var search = $.extend(true, criteria, {
            view : state.view
        });
        delete search.saved_search_id;
        saveSearch(search);
        return false;
    });

    // Edit Search
    $('#edit_search, #edit_search_email').live(BREW.events.click, function () {
        var email_results_immediately = $(this).attr('id') == 'edit_search_email' ? 'true' : 'false';
        var search = $.extend(true, criteria, {
            search_title: $form.find('input[name="search_title"]').val(),
            frequency: $form.find('select[name="frequency"]').val(),
            view: state.view,
            email_results_immediately: email_results_immediately
        });
        editSearch(search);
        return false;
    });

    // Trigger saved search dialog
    if (window.location.href.indexOf('auto_save') !== -1) {
        $('#save-link').trigger('click');
    }

})();
