(function($, window, document, undefined) {
    'use strict';

    var panorama;

    /**
	 * REWMap.Streetview
	 */
    this.Streetview = function (options) {
		
        // Streetview Options
        this.opts = $.extend({
            el : null,			// DOM Element	(Optional)
            lat : null,			// Latitude		(Required)
            lng : null,			// Longitude	(Required)
            onSuccess : null,	// Success Callback
            onFailure : null	// Failure Callback
        }, options);
		
        // Require Google Map API
        if (!REWMap.isLoaded) {
            REWMap.loadApi($.proxy(function () {
                this.init();
            }, this));
        } else {
            this.init();
        }
		
    };
	
    // Use Prototype...
    this.Streetview.prototype = {
			
        // Initialize
        init : function () {
			
            // LatLng Point
            this.setPoint(this.opts.lat, this.opts.lng);
			
            // Find Streetview Data
            var streetview = new google.maps.StreetViewService();
            streetview.getPanoramaByLocation(this.getPoint(), 50, $.proxy(function (data, status) {
                if (status === google.maps.GeocoderStatus.OK) {

                    // Panorama Data
                    var latlng = data.location.latLng;
					
                    // Load Panorama Imagery
                    this.loadPanorama(latlng.lat(), latlng.lng());

                    // onSuccess
                    if (typeof this.opts.onSuccess === 'function') this.opts.onSuccess.call(this, data);
					
                } else {
					
                    // onFailure
                    if (typeof this.opts.onFailure === 'function') this.opts.onFailure.call(this);
					
                }
            }, this));
			
        },
			
        // Set Point
        setPoint : function (lat, lng) {
            this.point = new google.maps.LatLng(lat, lng);
        },
		
        // Get Point
        getPoint : function () {
            return this.point;
        },

        // google.maps.StreetViewPanorama
        resize : function () {
            google.maps.event.trigger(panorama, 'resize');
        },

        // Load Panorama
        loadPanorama : function (lat, lng) {
			
            // Require DOM Element, Require Co-Ords
            if (!this.opts.el || !lat || !lng) return;
			
            // Center Point
            var center = new google.maps.LatLng(lat, lng);
			
            // Calculate Heading for POV
            var heading = google.maps.geometry.spherical.computeHeading(center, this.getPoint());
	
            // Setup Panorama
            panorama = new google.maps.StreetViewPanorama(this.opts.el, {
                position : center,
                //mode : 'html5',
                pov : {
                    heading : heading,
                    pitch : -10,
                    zoom : 1
                }
            });
			
        }
	
    };

}).apply(REWMap || {}, [jQuery, window, document]);