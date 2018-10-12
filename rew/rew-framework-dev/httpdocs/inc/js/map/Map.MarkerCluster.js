(function($, window, document, undefined) {
    'use strict';

    /**
	 * REWMap.MarkerCluster
	 */
    this.MarkerCluster = function (manager) {
        this.manager		= manager;	// REWMap.MarkerManager
        this.markers		= [];		// Array{REWMap.Marker}
        this.center			= null;		// Center Point
        this.bounds			= null;		// Cluster Bounds
        this.stacked		= false;	// Stacked Cluster
        this.titleStacked   = manager.opts.titleStacked;
        this.titleCluster   = manager.opts.titleCluster;
        this.minClusterSize	= manager.getMinClusterSize();
        this.averageCenter	= manager.getAvgCenter();
        this.marker			= new REWMap.Marker({
            map : manager.opts.map,
            icon : manager.icon,
            iconShadow : manager.iconShadow,
            visible : false,
            zIndex: manager.zIndex++,
            onClick : $.proxy(this.onClick, this)
        });
    };

    // Use Prototype...
    this.MarkerCluster.prototype = {
			
        // Get Markers
        getMarkers : function () {
            return this.markers;
        },
			
        // Get Center
        getCenter : function () {
            return this.center;
        },
	
        // Set Stacked
        setStacked : function (stacked) {
            // This Cluster is Now Stacked.. Hide Existing Markers
            if (this.stacked !== stacked && stacked === true) {
                var i = 0, l = this.markers.length;
                for (i; i < l; i++) {
                    this.markers[i].hide();
                }
            }
            this.stacked = stacked;
        },
		
        // Check for Markers with Exact Same Point
        pointExists : function (point) {
            var i, markers = this.markers;
            for (i = 0; i < markers.length; i++) {
                if (point.toString() === markers[i].getPoint().toString()) {
                    return true;
                }
            }
        },
	
        // Get Bounds
        getBounds : function () {
            var i, bounds = new google.maps.LatLngBounds(this.center, this.center), markers = this.getMarkers();
            for (i = 0; i < markers.length; i++) {
                bounds.extend(markers[i].getPoint());
            }
            return bounds;
        },
		
        // Remove Cluster
        remove : function () {
            this.marker.hide();
            this.markers = [];
            delete this.markers;
        },
		
        /**
		 * Determines if a marker lies within the cluster's bounds.
		 *
		 * @param {REWMap.Marker} marker The marker to check.
		 * @return {boolean} True if the marker lies in the bounds.
		 * @ignore
		 */
        isMarkerInClusterBounds : function (marker) {
            return this.bounds.contains(marker.getPoint());
        },
		
        /**
		 * Adds a marker to the cluster.
		 *
		 * @param {REWMap.Marker} marker The marker to be added.
		 * @return {boolean} True if the marker was added.
		 * @ignore
		 */
        addMarker : function (marker) {
            var i, mCount;
            if (this._isMarkerAlreadyAdded(marker)) {
                return false;
            }
            // Calculate Center & Bounds
            var point = marker.getPoint();
            if (!this.center) {
                this.center = point;
                this._calculateBounds();
            } else {
                if (this.averageCenter) {
                    var l = this.markers.length + 1;
                    var lat = (this.center.lat() * (l - 1) + point.lat()) / l;
                    var lng = (this.center.lng() * (l - 1) + point.lng()) / l;
                    this.center = new google.maps.LatLng(lat, lng);
                    this._calculateBounds();
                }
            }
            // Add Marker
            marker.isAdded = true;
            this.markers.push(marker);
            mCount = this.markers.length;
            var mz = this.manager.getMaxZoom();
            // Zoomed in past max zoom, so show the marker.
            if (this.stacked !== true && mz !== null && this.manager.getMap().getZoom() > mz) {
                marker.show();
				
                // Min cluster size not reached so show the marker.
            } else if (this.stacked !== true && mCount < this.minClusterSize) {
                marker.show();
				
                // Hide the markers that were showing.
            } else if (mCount === this.minClusterSize) {
                for (i = 0; i < mCount; i++) {
                    this.markers[i].hide();
                }
            } else {
                marker.hide();
            }
            this.render();
            return true;
			
        },
		
        /**
		 * Update Cluster Marker
		 */
        render : function () {
			
            // Total Markers
            var mCount = this.markers.length, showStacked = false;
			
            // Max zoom level has been exceeded
            var mz = this.manager.getMaxZoom();
            if (mz !== null && this.manager.getMap().getZoom() > mz) {
                showStacked = true;
                // Ignore for Stacked Cluster
                if (this.stacked !== true) {
                    this.marker.hide();
                    return;
                }
            }
			
            // Min cluster size not yet reached.
            if (mCount < this.minClusterSize) {
                showStacked = true;
                // Ignore for Stacked Cluster
                if (this.stacked !== true) {
                    this.marker.hide();
                    return;
                }
            }
			
            // Marker Position
            this.marker.setPoint(this.center.lat(), this.center.lng());
			
            // Single Marker
            if (mCount === 1) {
                var m = this.markers[0];
                this.marker.setTooltip(m.getTooltip());
                this.marker.setTitle(m.getTitle());
                this.marker.setIcon(this.manager.icon);
				
                // Stacked Marker
            } else if (this.stacked === true && showStacked === true) {
				
                // Generate Tooltip HTML
                var i = 0, l = this.markers.length, html = [];
                for (i; i < l; i++) {
                    html.push(this.markers[i].getTooltip());
                }
				
                // Set Tooltip HTML
                this.marker.setTooltip('<div class="popover stacked">\
						<header class="title">\
							<strong>' + this.titleStacked.replace('{x}', mCount) + '</strong>\
							<a href="javascript:void(0);" class="action-close hidden">&times;</a>\
						</header>\
						<div class="body">' + html.join('\n') + '</div>\
						<div class="tail"></div>\
					</div>');
				
                // Update Title
                //this.marker.setTitle(mCount + ' Properties Found');
				
                // Update Icon
                this.marker.setIcon(this.manager.iconStacked);
				
                // Marker Cluster
            } else {
				
                // Set Tooltip HTML
                this.marker.setTooltip('<div class="popover">\
					<header class="title">\
						<strong>' + this.titleCluster.replace('{x}', mCount) + '</strong>\
						<small>Click to Zoom In.</small>\
						<a href="javascript:void(0);" class="action-close hidden">&times;</a>\
					</header>\
					<div class="tail"></div>\
				</div>');
				
                // Update Title
                //this.marker.setTitle('Click to Zoom In');

                // Set Scale based on Logarithmic scaling between 2 and maxSize
                if (this.manager.iconCluster.scalability) {
                    if (mCount < this.manager.iconCluster.scalability.maxSize) {
                        // position will be between 2 and maxSize
                        var minSize = 2;
                        var maxSize = this.manager.iconCluster.scalability.maxSize;

                        // maxSize cannot be lower than 3 to avoid dividing by zero and other sizing issues
                        if (maxSize < 3) {
                            maxSize = 3;
                        }

                        // The result should be between minScale and maxScale
                        var minValue = Math.log(this.manager.iconCluster.scalability.minScale);
                        var maxValue = Math.log(this.manager.iconCluster.scalability.maxScale);

                        // calculate adjustment factor
                        var scale = (maxValue - minValue) / (maxSize - minSize);

                        this.manager.iconCluster.scale = Math.exp(minValue + scale * (mCount - minSize));
                    } else {
                        this.manager.iconCluster.scale = this.manager.iconCluster.scalability.maxScale;
                    }
                }

                // Update Icon
                this.marker.setIcon(this.manager.iconCluster);

                // Set Market Label
                this.marker.setLabel($.extend({
                    text: '' + mCount
                }, this.manager.opts.labelOptions));
				
            }
			
            // Show Marker
            this.marker.show();
			
        },
		
        // Marker Click
        onClick : function () {
			
            // Cluster Bounds
            var bounds = this.getBounds();
			
	        // Zoom into the cluster.
            var mz = this.manager.getMaxZoom(), gmap = this.manager.getMap();
			
            // Check Max Zoom
            if (mz === null || gmap.getZoom() <= mz) {
			
                // Fit Bounds
                gmap.fitBounds(bounds);
				
		        // Don't zoom beyond the max zoom level.
		        if (mz !== null && gmap.getZoom() > mz) {
		        	gmap.setZoom(mz + 1);
		        }
		        
            }
				
            // Show Marker Tooltip
            this.marker.showTooltip(true);
			
        },
		
        /**
		 * Calculates the extended bounds of the cluster with the grid.
		 */
        _calculateBounds: function () {
            var bounds = new google.maps.LatLngBounds(this.center, this.center);
		  	this.bounds = this.manager.extendBounds(bounds);
        },
		
        /**
		 * Determines if a marker has already been added to the cluster.
		 *
		 * @param {REWMap.Marker} marker The marker to check.
		 * @return {boolean} True if the marker has already been added.
		 */
        _isMarkerAlreadyAdded: function (marker) {
            var i;
            if (this.markers.indexOf) {
                return this.markers.indexOf(marker) !== -1;
            } else {
                for (i = 0; i < this.markers.length; i++) {
                    if (marker === this.markers[i]) {
                        return true;
                    }
                }
            }
            return false;
        }
		
    };

}).apply(REWMap || {}, [jQuery, window, document]);