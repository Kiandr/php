(function($, window, document, undefined) {
    'use strict';
	
    /**
	 * REWMap.Directions
	 */
    this.Directions = function (options) {
		
        // Directions Options
        this.opts = $.extend(true, {
            el : null,			// DOM Element	(Optional)
            to : null,			// To Address	(Required)
            from : null,		// From Address	(Required)
            unit : 'imperial',	// Unit System: 'imperial' or 'metric'
            renderer : {		// Renderer Options, @see https://developers.google.com/maps/documentation/javascript/reference#DirectionsRendererOptions
                draggable : true,	// Draggable Points
                map: null,			// google.maps.Map
                panel: null			// DOM Element
            },
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
    this.Directions.prototype = {
			
        // Initialize
        init : function () {
			
            // Google Directions Service
            this.directions = new google.maps.DirectionsService();
			
            // Directions Renderer
            this.renderer = new google.maps.DirectionsRenderer(this.opts.renderer);
			
            // Get Directions
            if (this.opts.from && this.opts.to) {
                this.getDirections(this.opts.from, this.opts.to);
            }
			
        },
			
        // Get Directions
        getDirections : function (from, to) {
			
            // Require Arguments
            if (!from || !to) return;
			
            // Request Directions
            this.directions.route({
                origin : from,
                destination : to,
                unitSystem : this.getUnitId(this.opts.unit),
                travelMode : google.maps.TravelMode.DRIVING
            }, $.proxy(function (result, status) {
                if (status === google.maps.DirectionsStatus.OK) {
					
                    // onSuccess
                    if (typeof this.opts.onSuccess === 'function') this.opts.onSuccess.call(this, result);
					
                    // Set Directions
                    this.renderer.setDirections(result);
					
                } else {
					
                    // onFailure
                    if (typeof this.opts.onFailure === 'function') this.opts.onFailure.call(this, this.getErrorMsg(status));
					
                }
            }, this));
			
        },
			
        // Get Error Message
        getErrorMsg : function (error) {
            switch (error) {

            // At least one of the origin, destination, or waypoints could not be geocoded.
            case 'NOT_FOUND' :
            case google.maps.DirectionsStatus.NOT_FOUND :
                return 'An error occurred. Your origin or destination could not be found.';
	
                // No route could be found between the origin and destination.
            case 'ZERO_RESULTS' :
            case google.maps.DirectionsStatus.ZERO_RESULTS :
                return 'Directions could not be found between your origin and destination.';
	
                // The webpage is not allowed to use the directions service.
            case 'REQUEST_DENIED' :
            case google.maps.DirectionsStatus.REQUEST_DENIED :
                return 'An error has occurred. Your request was denied.';
	
                // The webpage has gone over the requests limit in too short a period of time.
            case 'OVER_QUERY_LIMIT' :
            case google.maps.DirectionsStatus.OVER_QUERY_LIMIT :
                return 'Your request could not be processed right now. Please try again later.';
	
                // A directions request could not be processed due to a server error. The request may succeed if you try again.
            case 'UNKNOWN_ERROR' :
            case google.maps.DirectionsStatus.UNKNOWN_ERROR :
                return 'An error occurred while processing your request. Please try again.';
			
                // The DirectionsRequest provided was invalid.
            case 'INVALID_REQUEST' :
            case google.maps.DirectionsStatus.INVALID_REQUEST :
                return 'An error occurred while processing your request';
			
            }
        },
		
        // Get Unit Id
        getUnitId : function (unit) {
            switch (unit) {
            case 'imperial' :
            case google.maps.UnitSystem.IMPERIAL :
                return google.maps.UnitSystem.IMPERIAL;
            case 'metric' :
            case google.maps.UnitSystem.METRIC :
                return google.maps.UnitSystem.METRIC;
            }
        }
	
    };

}).apply(REWMap || {}, [jQuery, window, document]);