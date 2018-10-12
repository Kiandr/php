(function($, window, document, undefined) {
    'use strict';
	
    /**
	 * REWMap.MarkerManager
	 */
    this.MarkerManager = function (options) {
		
        // google.maps.OverlayView, @see https://developers.google.com/maps/documentation/javascript/reference#OverlayView
        $.extend(true, REWMap.MarkerManager.prototype, google.maps.OverlayView.prototype);

        // MarkerManager Defaults
        var defaults = {
            map: null,							// REWMap (Required)
            bounds: true,						// Fit to Bounds
            cluster: false,						// Use Clustering
            markers: [],						// REWMap Markers
            stack: true,						// Stack Markers
            icon: '/img/map/marker-home@2x.png',	// Icon Image
            iconWidth: 22,							// Icon Width (in Pixels)
            iconHeight: 25,							// Icon Height (in Pixels)
            iconCluster: {
                url: '/img/map/cluster-home@2x.png', // Cluster Icon Image
                labelOrigin: { x: 16, y: 11 }, // Cluster label position
                scaledSize: { width: 31, height: 25 } // Cluster Icon Width & height (in Pixels)
            },
            labelOptions: {
              fontSize: '12px',
              color: 'white' // Marker/Cluster label color
            },
            iconStacked: '/img/map/stacked-home@2x.png',	// Stacked Icon Image
            iconStackedWidth: 20,					// Stacked Icon Width (in Pixels)
            iconStackedHeight: 23,					// Stacked Icon Height (in Pixels)
            iconShadow: '/img/map/shadow@2x.png',	// Shadow Icon
            iconShadowWidth: 14,				// Shadow Icon Width (in Pixels)
            iconShadowHeight: 6,				// Shadow Icon Height (in Pixels)
            iconShadowAnchorX: 7,				// Shadow Icon Anchor
            iconShadowAnchorY: 3,				// Shadow Icon Anchor
            maxZoom: 14,						// Max. zoom level which clustering is enabled at. If null, enabled at all zoom levels.
            gridSize: 60,						// The grid size of a cluster in pixels. The grid is a square.
            minClusterSize: 3,					// Minimum number of markers needed in a cluster to be visible.
            averageCenter: false,				// Position of cluster marker should be average position of all markers. If false, location of first marker is used.
            titleStacked: '{x} Properties Found at This Location.',	// This is the title used for stacked markers
            titleCluster: '{x} Properties Found.',					// This is the title used for cluster markers
            in_radius: false					// This will only show markers that within a radius. It can have markers loaded outside of the radius but they will not display. Expects a radius search on page.
        };
		
        // MarkerManager Options
        this.opts = $.extend({}, defaults, options);

        // Map Icon
        if (typeof this.opts.icon === 'object') {
            this.icon = this.opts.icon;
        } else {
            this.icon = new google.maps.MarkerImage(this.opts.icon, null, null, null, new google.maps.Size(this.opts.iconWidth, this.opts.iconHeight));
        }
        this.iconShadow = new google.maps.MarkerImage(this.opts.iconShadow, null, null, new google.maps.Point (this.opts.iconShadowAnchorX, this.opts.iconShadowAnchorY), new google.maps.Size(this.opts.iconShadowWidth, this.opts.iconShadowHeight));
        if (typeof this.opts.iconCluster === 'object') {
            this.iconCluster = this.opts.iconCluster;
        } else {
            this.iconCluster = new google.maps.MarkerImage(this.opts.iconCluster, null, null, null, new google.maps.Size(this.opts.iconClusterWidth, this.opts.iconClusterHeight));
        }
        if (typeof this.opts.iconStacked === 'object') {
            this.iconStacked = this.opts.iconStacked;
        } else {
            this.iconStacked = new google.maps.MarkerImage(this.opts.iconStacked, null, null, null, new google.maps.Size(this.opts.iconStackedWidth, this.opts.iconStackedHeight));
        }
		
        // REWMap Markers
        this.markers = [];
		
        // REWMap Clusters
        this.clusters = [];
		
        // zIndex (Increments with each marker)
        this.zIndex = 101;
		
        // Set Map, This will cause 'onAdd' to be called
        var gmap = this.opts.map.getMap();
        this.setMap(gmap);
		
    };
	
    // Use Prototype...
    this.MarkerManager.prototype = {
		
        // Implementation of the draw interface method.
        draw : function () {},
		
        // Implementation of the onAdd interface method.
        onAdd : function () {
			
            // Load Markers
            if (this.opts.markers) this.load(this.opts.markers);
			
            // Google Map Event Listeners
            var manager = this;
            this.listeners = [
               	google.maps.event.addListener(this.getMap(), 'zoom_changed', function () {
               		manager.removeClusters(true);
       				// Workaround for this Google bug: when map is at level 0 and "-" of
       				// zoom slider is clicked, a "zoom_changed" event is fired even though
       				// the map doesn't zoom out any further. In this situation, no "idle"
       				// event is triggered so the cluster markers that have been removed
       				// do not get redrawn.
           			if (this.getZoom() === 0) {
       					google.maps.event.trigger(this, 'idle');
           			}
               	}),
                // Force map re-size
                google.maps.event.addListenerOnce(this.getMap(), 'idle', function () {
                    var $map = $(this.getDiv());
                    $map.height($map.height() + 1);
                    google.maps.event.trigger(this, 'resize');
                }),
                // Plot markers
               	google.maps.event.addListener(this.getMap(), 'idle', function () {
               		manager.plot();
               	})
           	];
			
        },
		
        // Implementation of the onRemove interface method.
        onRemove : function () {
			
            // Remove Clusters
            this.removeClusters(false);

            // Remove Event Listeners
            if (this.listeners.length > 0) {
                var listener = null;
                while (listener = this.listeners.pop()) {
                    google.maps.event.removeListener(listener);
                }
            }
            this.listeners = [];
			
        },
		
        // Clear Manager
        clear : function () {
            this.removeMarkers();
            this.removeClusters();
        },
		
        // Remove Markers
        removeMarkers : function () {
            if (this.markers.length > 0) {
                var marker = null;
                while (marker = this.markers.pop()) {
                    marker.hide();
                }
            }
            this.markers = [];
        },
		
        // Remove Markers
        removeClusters : function (hide) {
            // Remove Clusters
            if (this.clusters.length > 0) {
                var cluster = null;
                while (cluster = this.clusters.pop()) {
                    cluster.remove();
                }
            }
            this.clusters = [];
            // Reset the markers to not be added and to be removed from the map.
            var i = 0, l = this.markers.length, marker; 
            for (i; i < l; i++) {
                marker = this.markers[i];
                marker.isAdded = false;
                if (hide) {
                    marker.hide();
                }
            }
        },

        // Get Markers
        getMarkers : function () {
            return this.markers;
        },
		
        // Get Max Zoom Level
        getMaxZoom : function () {
            return this.opts.maxZoom;
        },
		
        // Get Min Cluster Size
        getMinClusterSize : function () {
            return this.opts.minClusterSize;
        },
		
        // Get Average Center Point
        getAvgCenter : function () {
            return this.opts.averageCenter;
        },
	
        // Load Marker Data
        load : function (markers) {

            // Remove Markers
            this.removeMarkers(true);
			
            // Map Bounds
            var bounds = new google.maps.LatLngBounds();
				
            // Process Data
            var data = null;
            while (data = markers.pop()) {
				
                // Check if we already placed that marker
                if (typeof data.id !== 'undefined') {
                    if (typeof this.marker_ids !== 'undefined' && this.marker_ids.indexOf(data.id) >= 0) {
                        continue;
                    }
                }
				
                // Check Icon Over-Rides
                if (data.icon) {
				    data.iconWidth = data.iconWidth > 0 ? data.iconWidth : this.opts.iconWidth;
				    data.iconHeight = data.iconHeight > 0 ? data.iconHeight : this.opts.iconHeight;
				    data.icon = new google.maps.MarkerImage(data.icon, null, null, null, new google.maps.Size(data.iconWidth, data.iconHeight));
                }
				
                // Create Marker
                var marker = new REWMap.Marker($.extend({
                    map: this.opts.map,
                    icon: this.icon,
                    iconShadow: this.iconShadow,
                    visible: false,
                    zIndex: this.zIndex++
                }, data));
				
                // Save the id
                if (typeof data.id !== 'undefined') {
                    if (typeof this.marker_ids !== 'object') {
                        this.marker_ids = new Array();
                        this.marker_ids.push(data.id);
                    } else {
                        this.marker_ids.push(data.id);
                    }
                }
				
                // Add to Manager
                this.markers.push(marker);
				
				
                // Add to Bounds
                bounds.extend(marker.getPoint());
				
            }
			
            // Fit to Bounds
            if (this.opts.bounds && !bounds.isEmpty()) {
                this.getMap().fitBounds(bounds);
            }
			
            // Plot Data
            this.plot();
			
        },
		
        // Plot Markers
        plot : function () {
            // Get our current map view bounds. Create a new bounds object so we don't affect the map.
            // See Comments 9 & 11 on Issue 3651 relating to this workaround for a Google Maps bug:
            var gmap = this.getMap();
            if (gmap.getZoom() > 3) {
                var bounds = gmap.getBounds();
                bounds = new google.maps.LatLngBounds(bounds.getSouthWest(), bounds.getNorthEast());
            } else {
                var bounds = new google.maps.LatLngBounds(new google.maps.LatLng(85.02070771743472, -178.48388434375), new google.maps.LatLng(-85.08136444384544, 178.00048865625));
            }
            bounds = this.extendBounds(bounds);
			
            if (this.opts.in_radius) {
                var circles = this.opts.map.radiusControl.getSearches(),
                    circle = circles[0] || {};
            }
			
            // Process Markers
            var i = 0, l = this.markers.length, marker;
            for (i; i < l; i++) {
                marker = this.markers[i];
                if (this.opts.in_radius) {
                    if (typeof circle.contains != 'undefined' && !marker.isAdded && circle.contains(marker.getPoint())) {
                        this._addToClosestCluster(marker);
                    }
                } else if (!marker.isAdded && bounds.contains(marker.getPoint())) {
                    this._addToClosestCluster(marker);
                }
            }
			
        },
		
        /**
		 * Returns the current bounds extended by the grid size.
		 *
		 * @param {google.maps.LatLngBounds} bounds The bounds to extend.
		 * @return {google.maps.LatLngBounds} The extended bounds.
		 * @ignore
		 */
        extendBounds : function (bounds) {
			
            // Map Projection
            var projection = this.getProjection();
		  
            // Turn the bounds into latlng.
            var tr = new google.maps.LatLng(bounds.getNorthEast().lat(), bounds.getNorthEast().lng());
            var bl = new google.maps.LatLng(bounds.getSouthWest().lat(), bounds.getSouthWest().lng());
			
            // Convert the points to pixels and the extend out by the grid size.
            var trPix = projection.fromLatLngToDivPixel(tr);
            trPix.x += this.opts.gridSize;
            trPix.y -= this.opts.gridSize;
			
            var blPix = projection.fromLatLngToDivPixel(bl);
            blPix.x -= this.opts.gridSize;
            blPix.y += this.opts.gridSize;
			
            // Convert the pixel points back to LatLng
            var ne = projection.fromDivPixelToLatLng(trPix);
            var sw = projection.fromDivPixelToLatLng(blPix);
			
            // Extend the bounds to contain the new bounds.
            bounds.extend(ne);
            bounds.extend(sw);
			
            // Return Bounds
            return bounds;
			
        },
		
        /**
		 * Calculates the distance between two latlng locations in km.
		 *
		 * @param {google.maps.LatLng} p1 The first lat lng point.
		 * @param {google.maps.LatLng} p2 The second lat lng point.
		 * @return {number} The distance between the two points in km.
		 * @see http://www.movable-type.co.uk/scripts/latlong.html
		 */
        _distanceBetweenPoints: function (p1, p2) {
            var R = 6371; // Radius of the Earth in km
            var dLat = (p2.lat() - p1.lat()) * Math.PI / 180;
            var dLon = (p2.lng() - p1.lng()) * Math.PI / 180;
            var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
     Math.cos(p1.lat() * Math.PI / 180) * Math.cos(p2.lat() * Math.PI / 180) *
     Math.sin(dLon / 2) * Math.sin(dLon / 2);
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return (R * c);
        },
		
        /**
		 * Adds a marker to a cluster, or creates a new cluster.
		 *
		 * @param {REWMap.Marker} marker The marker to add.
		 */
        _addToClosestCluster: function (marker) {

            // Find Closest Cluster...
            var i, d, cluster, center, clusterToAddTo = null;
            var distance = 40000; // Some large number
            for (i = 0; i < this.clusters.length; i++) {
                cluster = this.clusters[i];
                center = cluster.getCenter();
                if (center) {
                    d = this._distanceBetweenPoints(center, marker.getPoint());
                    // Marker Is Exact Same Point!
                    if (d === 0 && this.opts.stack !== false) {
                        cluster.setStacked(true);
                        cluster.addMarker(marker);
                        return;
                    }
                    // Marker Is In Cluster's Distance!
                    if (cluster.stacked !== true && d < distance) {
                        distance = d;
                        clusterToAddTo = cluster;
                    }
                }
            }
				
            // Clustering Enabled, Add to Nearest Cluster
            if (this.opts.cluster === true && clusterToAddTo && clusterToAddTo.isMarkerInClusterBounds(marker)) {
                clusterToAddTo.addMarker(marker);
				
                // New Cluster
            } else {
                cluster = new REWMap.MarkerCluster(this);
                cluster.addMarker(marker);
                this.clusters.push(cluster);
				
            }
			
        }

    };

}).apply(REWMap || {}, [jQuery, window, document]);