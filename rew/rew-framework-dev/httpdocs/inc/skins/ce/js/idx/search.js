/* global {String} BREW.events.click */
var $form = $('form.idx-search');
(function () {
    'use strict';

    /**
     * Map search controls:
     *  - Polygon/Radius/Bounds
     */
    if (typeof REWMap !== "undefined") {
        for(var map in $mapList) {
            $mapList[map].REWMap('setOptions', {
                'onInit': function () {
                    var $mapTools = $('#map-draw-controls');
                    if ($mapTools.length > 0) {
                        var mapTools = $mapTools.removeClass('hidden').get(0);
                        this.gmap.controls[google.maps.ControlPosition.TOP_LEFT].push(mapTools);
                    }
                }
            });
        };
    }

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
     *      <a href="#map" class="view-map">
     *  </div>
     */

    // Search result criteria
    var criteria = window.criteria || {};

    // Toggle map display
    $('a.view-map').click(function (e) {
        var selected_feed = e.currentTarget.hash.substr(1);
        $map = $mapList[selected_feed];
        var showMap = $map.hasClass('hidden');
        var linkText = showMap ? 'Hide Map' : 'Show Map';
        var isHuman = e.originalEvent !== undefined;
        $(this).toggleClass('current', showMap).find('span').text(linkText);
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

    // Open map
    var showMap = (criteria && criteria.map && criteria.map.open == 1);
    var showMapCookie = BREW.Cookie('results-map');
    if ( (showMap && showMapCookie == null) || parseInt(showMapCookie) == 1) {
        $('a.view-map').trigger(BREW.events.click);
    }

    /**
     * Search save controls:
     *  <a id="save-search">
     *  <a id="edit-search">
     */

    // Create saved search link
    $('.save--search').on(BREW.events.click, function (e) {
        var feed = e.currentTarget.hash.substr(1);
        var search = $.extend(true, criteria, { view: view });
        saveSearch(search[feed]);
        return false;
    });

    // Edit saved search link
    $('#edit-search, #edit-search-email').live(BREW.events.click, function (e) {
        var email_results_immediately = $(this).attr('id') == 'edit-search-email' ? 'true' : 'false';
        var feed = e.currentTarget.hash.substr(1);
        var search = $.extend(true, criteria, {
            search_title: $form.find('input[name="search_title"]').val(),
            frequency: $form.find('select[name="frequency"]').val(),
            view: view,
            email_results_immediately: email_results_immediately
        });
        editSearch(search[feed]);
        return false;
    });

    // Trigger saved search dialog for ?auto_saved URLs
    if (window.location.href.indexOf('auto_save') !== -1) {
        $('#save-search').trigger(BREW.events.click);
    }

})();