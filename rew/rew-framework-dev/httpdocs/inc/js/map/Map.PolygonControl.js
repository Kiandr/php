(function($, window, document, undefined) {
    'use strict';
	
    /**
	 * REWMap.PolygonControl
	 */
    this.PolygonControl = function (options) {
		
        // Control Options
        this.opts = $.extend({
            drawText: 'Draw Polygon',
            editText: 'Edit Polygon',
            helpText: 'Click on the map to draw your polygon search.'
        }, options);
		
        // Call Parent
        REWMap.SearchControl.call(this, this.opts);
		
    };
	
    // Use Prototype...
    this.PolygonControl.prototype = $.extend({}, this.SearchControl.prototype, {
		
        // Get Polygon Size
        computeSize : function (search) {
            return google.maps.geometry.spherical.computeArea(search.getPath());
        },
		
        // Get Map Polygons (Serialize Polygon Searches as JSON Array)
        serialize : function () {
            var polygons = this.getSearches(), json = [];
		    if (polygons && polygons.length > 0) {
		    	var len = polygons.length, i = 0;
                for (i; i < len; i++) {
                    // Serialize Polygon Points to WKT
                    var polygon = polygons[i], points = polygon.getPath().getArray(), l = points.length, p = 0, wkt = [];
                    for (p; p < l; p++) {
                        var point = points[p];
                        if (point) wkt.push(point.lat() + ' ' + point.lng());
                    }
                    var l = wkt.length;
                    if (l > 0) {
                        // Require Complete Polygon
                        if (wkt[0] !== wkt[l - 1]) {
                            wkt.push(wkt[0]);
                        }
                        // Add to JSON Array
                        json.push(wkt.join(','));
                    }
                }
		    }
		    return json.length > 0 ? '["' + json.join('", "') + '"]' : null;
        },
		
        // Draw Mode
        draw : function () {
			
            // Enable Drawing Mode
            this.controls.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);
			
            // Polygon Drawn
            this.listener = google.maps.event.addListenerOnce(this.controls, 'polygoncomplete', $.proxy(function (polygon) {
                var path = polygon.getPath().getArray(), f = path[0];
                if (path.length > 2) {
                    path.push(f);
                    polygon.setEditable(false);
                    this.searches.push(polygon);
                } else {
                    polygon.setMap(null);
                }
                this.disable();
                if (typeof this.opts.onDraw === 'function') this.opts.onDraw.call(this, polygon);
            }, this));
			
        },
		
        // Load Polygon Data
        load : function (polygons) {
			
            // Polygons
            var i = polygons.length - 1;
            for (i; i >= 0; i--) {
				
                // Points
                var poly = polygons[i], paths = [], v = poly.length - 1;
                for (v; v >= 0; v--) {
                    var path = poly[v];
                    paths.push(new google.maps.LatLng(path.lat, path.lng));
                }
				
                // Draw Polygon
                var polygon = new google.maps.Polygon($.extend({}, this.controls.get('polygonOptions'), {
                    map: this.controls.getMap(),
                    paths: paths,
                    editable: false
                }));
				
                // Add to Searches
                this.searches.push(polygon);
				
                // Callback for drawn polygon
                if (typeof this.opts.onDraw === 'function') this.opts.onDraw.call(this, polygon);

            }
			
            // Refresh List
            this.refresh();
			
        }
		
    });
	
}).apply(REWMap || {}, [jQuery, window, document]);