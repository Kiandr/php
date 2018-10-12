/* global {String} BREW.events.click */
var $form = $('form.idx-search');
(function () {
    'use strict';

    /**
     * Map search controls:
     *  - Polygon/Radius/Bounds
     */
    $map.REWMap('setOptions', {
        'onInit' : function () {
            var $mapTools = $('#map-draw-controls');
            if ($mapTools.length > 0) {
                var mapTools = $mapTools.removeClass('hidden').get(0);
                this.gmap.controls[google.maps.ControlPosition.TOP_LEFT].push(mapTools);
            }
        }
    });

    /**
     * Sync map criteria
     */
    $form.on('submit', function () {

        // Map center point
        var center = $map.REWMap('getCenter');
        if (center) $form.find('input[name="map[latitude]"]').val(center.lat());
        if (center) $form.find('input[name="map[longitude]"]').val(center.lng());

        // Map zoom level
        var zoom = $map.REWMap('getZoom');
        if (zoom) $form.find('input[name="map[zoom]"]').val(zoom);

        // Map boundary
        var bounds = $map.REWMap('getBounds');
        $form.find('input[name="map[ne]"]').val(bounds ? bounds.getNorthEast().toUrlValue() : '');
        $form.find('input[name="map[sw]"]').val(bounds ? bounds.getSouthWest().toUrlValue() : '');
        $form.find('input[name="map[bounds]"]').val($form.triggerHandler('checkMap') === 'Bounds' ? 1 : 0);

        // Polygon searches
        var polygons = $map.REWMap('getPolygons');
        if (typeof polygons !== 'undefined') {
            $form.find('input[name="map[polygon]"]').val(polygons ? polygons : '');
        }

        // Radius searches
        var radiuses = $map.REWMap('getRadiuses');
        if (typeof radiuses !== 'undefined') {
            $form.find('input[name="map[radius]"]').val(radiuses ? radiuses : '');
        }

    });

    /**
     * Search view controls:
     *  <div id="search-toolbar">
     *      <a href="#grid" class="view">
     *      <a href="#detailed" class="view">
     *      <a href="#map" class="view-map">
     *  </div>
     */

    // Search toolbar
    var $toolbar = $('#search-toolbar');

    // Search result criteria
    var criteria = window.criteria || {};

    // Search result view mode
    var view = window.view || '';
    if (window.location.hash.indexOf('#') != -1) {
        view = window.location.hash.substr(window.location.hash.indexOf('#') + 1);
    }

    // Toggle results view
    $toolbar.on(BREW.events.click, 'a.view', function (e) {
        $(this).addClass('mnu-item--cur').siblings('a.view').removeClass('mnu-item--cur');
        var viewMode = $(this).attr('href').substr(1);
        var $listings = $('.listing');
        var no_overlay = $listings.hasClass('listing-no-overlay');
        $listings.attr('class','listing col');
        // Grid view
        if (viewMode === 'grid') {
            if (no_overlay) {
                $listings.addClass('grid-view--stacked w1/1-sm w1/3 w1/2-md listing-no-overlay');
            } else {
                $listings.addClass('stk w1/1-sm w1/3 w1/2-md');
            }
        }
        // List view
        if (viewMode === 'detailed') {
            if (no_overlay) {
                $listings.addClass('wide-view--sideXside w1/1-sm w1/1 listing-no-overlay');
            } else {
                $listings.addClass('col stk w1/1-sm listing w1/1 wide-view');
            }
        }
        BREW.Cookie('results-view', viewMode);
        e.preventDefault();
    });

    // Toggle map display
    $toolbar.on(BREW.events.click, 'a.view-map', function (e) {
        var showMap = $map.hasClass('hidden');
        var isHuman = e.originalEvent !== undefined;
        $(this).toggleClass('mnu-item--cur', showMap);
        $map.REWMap(showMap ? 'show' : 'hide', function () {
            $('#field-polygon').toggleClass('hidden', !showMap);
            $('#field-radius').toggleClass('hidden', !showMap);
            $('#field-bounds').toggleClass('hidden', !showMap);
            $('body').toggleClass('map-displayed', showMap);
            if (isHuman)
                BREW.Cookie('results-map', showMap ? 1 : 0);
        });
        e.preventDefault();
    });

    // Trigger search results view change
    $toolbar.find('a.view[href="#' + view + '"]').trigger(BREW.events.click);

    // Open map
    var showMap = (criteria && criteria.map && criteria.map.open == 1);
    var showMapCookie = BREW.Cookie('results-map');
    if ( (showMap && showMapCookie == null) || parseInt(showMapCookie) == 1) {
        $toolbar.find('a.view-map').trigger(BREW.events.click);
    }

    /**
     * Search save controls:
     *  <a id="save-search">
     *  <a id="edit-search">
     */
   
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
	
    // Create saved search link
    $('#save-search').on(BREW.events.click, function () {
        var search = $.extend(true, criteria[feed], { view: view });
        delete search.saved_search_id;
        saveSearch(search);
        return false;
    });

    // Edit saved search link
    $('#edit-search, #edit-search-email').live(BREW.events.click, function () {
        var email_results_immediately = $(this).attr('id') == 'edit-search-email' ? 'true' : 'false';
        var search = $.extend(true, criteria[feed], {
            search_title: $form.find('input[name="search_title"]').val(),
            frequency: $form.find('select[name="frequency"]').val(),
            view: view,
            email_results_immediately: email_results_immediately
        });
        editSearch(search);
        return false;
    });

    // Trigger saved search dialog for ?auto_saved URLs
    if (window.location.href.indexOf('auto_save') !== -1) {
        $('#save-search').trigger(BREW.events.click);
    }

})();
