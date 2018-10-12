(function($, window, document, undefined) {
    'use strict';

    /**
	 * REWMap.RadiusControl
	 */
    this.RadiusControl = function (options) {
		
        // Control Options
        this.opts = $.extend({
            drawText: 'Draw Radius',
            editText: 'Edit Radius',
            helpText: 'Click on the map to draw your radius search.'
        }, options);
		
        // Call Parent
        REWMap.SearchControl.call(this, this.opts);
		
    };
	
    // Use Prototype...
    this.RadiusControl.prototype = $.extend({}, this.SearchControl.prototype, {
		
        // Get Radius Size
        computeSize : function (search) {
            return search.getRadius();
        },
		
        // Get Map Radiuses (Serialize Radius Searches as JSON Array)
        serialize : function () {
            var radiuses = this.getSearches(), json = [];
		    if (radiuses && radiuses.length > 0) {
		    	var l = radiuses.length, i = 0;
                for (i; i < l; i++) {
                    var radius = radiuses[i], c = radius.getCenter();
                    json.push(c.lat() + ',' + c.lng() + ',' + parseFloat(radius.getRadius() / 1609)); // Convert Meters to Miles
                }
		    }
		    return json.length > 0 ? '["' + json.join('", "') + '"]' : null;
        },
		
        // Draw Mode
        draw : function () {
			
            // Enable Drawing Mode
            this.controls.setDrawingMode(google.maps.drawing.OverlayType.CIRCLE);
			
            // Radius Drawn
            this.listener = google.maps.event.addListenerOnce(this.controls, 'circlecomplete', $.proxy(function (radius) {
                radius.setEditable(false);
                this.searches.push(radius);
                this.disable();
                if (typeof this.opts.onDraw === 'function') this.opts.onDraw.call(this, radius);
            }, this));
			
        },
		
        // Load Radiuses
        load : function (radiuses) {
			
            // Radiuses
            var i = radiuses.length - 1;
            for (i; i >= 0; i--) {
                var r = radiuses[i];
				
                // Draw Radius
                var radius = new google.maps.Circle($.extend({}, this.controls.get('circleOptions'), {
                    map: this.controls.getMap(),
                    radius: (r.radius * 1609), // Convert from Miles to Meters
                    center: new google.maps.LatLng(r.lat, r.lng),
                    editable: r.edit ? true : false
                }));
				
                // Add to Searches
                this.searches.push(radius);
				
                // Callback for drawn radius
                if (typeof this.opts.onDraw === 'function') this.opts.onDraw.call(this, radius);

            }
			
            // Refresh List
            this.refresh();
			
        }
	
    });
	
}).apply(REWMap || {}, [jQuery, window, document]);