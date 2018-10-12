(function($, window, document, undefined) {
    'use strict';
	
    /**
	 * REWMap.Marker
	 */
    this.Marker = function (options) {

        // Marker Defaults
        var defaults = {
            map: null,			// REWMap (Required)
            lat: null,			// Latitude
            lng: null,			// Longitude
            icon: null,			// Marker Icon
            iconShadow: null,	// Marker Icon Shadow
            title: '',			// Marker Title
            tooltip: false,		// Tooltip HTML
            visible: true,		// Show Marker
            zIndex: 101,		// Marker z-Index (Must be greater than zero)
            onClick : function () {			// Click Callback
                this.showTooltip(true); // Show Sticky Tooltip
            },
            onMouseOver : function () {		// Mouse Over Callback
                this.showTooltip(); // Show Tooltip
            },
            onMouseOut : function () {		// Mouse Out Callback
                this.hideTooltip(); // Hide Tooltip
            }
        };
		
        // Marker Options
        this.opts = $.extend({}, defaults, options);
		
        // Point
        if (this.opts.lat && this.opts.lng) {
            this.point = new google.maps.LatLng(
                this.opts.lat,
                this.opts.lng
            );
        } else {
            this.point = null;
        }
		
        // Marker
        this.marker = new google.maps.Marker({
            map: this.opts.visible === true ? this.opts.map.getMap() : null,
            icon: this.opts.icon,
            shadow: this.opts.iconShadow,
            optimized: false,
            title: this.opts.title,
            position: this.point,
            zIndex: this.opts.zIndex,
            label: this.opts.label || null,
            //animation: google.maps.Animation.DROP
        });
		
        // Bind Events
        this.bindEvents();
		
    };
	
    // Use Prototype...
    this.Marker.prototype = {
		
        // Hide Marker
        hide : function () {
            this.marker.setMap(null);
            if (this.label) this.label.setMap(null);
        },
		
        // Show Marker
        show : function () {
            var gmap = this.opts.map.getMap();
            this.marker.setMap(gmap);
            if (this.label) this.label.setMap(gmap);
        },
			
        // Get Marker
        getMarker : function () {
            return this.marker;
        },

        // Get Point
        getPoint : function () {
            return this.point;
        },
		
        // Get Tooltip HTML
        getTooltip : function () {
            return this.opts.tooltip;
        },
		
        // Get Marker Title
        getTitle : function () {
            return this.opts.title;
        },
		
        // Set Marker Label
        setLabel : function (label) {
            this.marker.setLabel(label);
        },
		
        // Set Point
        setPoint : function (lat, lng) {
            this.point = new google.maps.LatLng(lat, lng);
            this.marker.setPosition(this.point);
        },
		
        // Set Tooltip HTML
        setTooltip : function (html) {
            this.opts.tooltip = html;
        },
		
        // Set Marker Title
        setTitle : function (title) {
            this.opts.title = title;
            this.marker.setTitle(title);
        },
		
        // Set Marker Icon
        setIcon : function (icon) {
            this.marker.setIcon(icon);
        },
		
        // Select Marker
        select : function () {
            var map = this.marker.getMap();
            // Check Marker Visible
            if (!map.getBounds().contains(this.point)) { 
                // Pan to Point
                map.panTo(this.point);
                // Wait Until Finished..
                google.maps.event.addListenerOnce(map, 'idle', $.proxy(function() {
                    // Show Tooltip 
                    this.showTooltip(true);
                }, this));
            } else {
                // Show Tooltip
                this.showTooltip(true);
            }
        },
		
        // Show Tooltip
        showTooltip : function (sticky) {
            var offset = this.getOffset(), tooltip = this.opts.map.getTooltip();
            if (offset && tooltip && this.opts.tooltip && (sticky || !tooltip.getSticky())) {
                tooltip.setHtml(this.opts.tooltip);
                tooltip.setPosition(offset);
                tooltip.show(sticky);
            }
        },
		
        // Hide Tooltip
        hideTooltip : function () {
            var tooltip = this.opts.map.getTooltip();
            if (tooltip) tooltip.hide();
        },
		
        // Marker Offset (in Pixels)
        getOffset : function () {
            // Get Map
            var gmap = this.marker.getMap();
            if (!gmap) return null;
            // Position Details
            var scale = Math.pow(2, gmap.getZoom()),
                nw = new google.maps.LatLng(
                    gmap.getBounds().getNorthEast().lat(),
                    gmap.getBounds().getSouthWest().lng()
                ),
                wnw = gmap.getProjection().fromLatLngToPoint(nw),
                w = gmap.getProjection().fromLatLngToPoint(this.marker.getPosition());
            // Calculate Offset (in Pixels)
            return {
			    x: $(gmap.getDiv()).offset().left + Math.floor((w.x - wnw.x) * scale),
			    y: $(gmap.getDiv()).offset().top + Math.floor((w.y - wnw.y) * scale)
            };
        },
		
        // Bind Events
        bindEvents : function () {
			
            // Marker Click Event
            if (typeof this.opts.onClick === 'function') {
                google.maps.event.addListener(this.marker, 'click', $.proxy(this.opts.onClick, this));
            }
			
            // Mouse Over Event
            if (!REWMap.mobile && typeof this.opts.onMouseOver === 'function') {
                google.maps.event.addListener(this.marker, 'mouseover', $.proxy(this.opts.onMouseOver, this));
            }
			
            // Mouse Out Event
            if (!REWMap.mobile && typeof this.opts.onMouseOut === 'function') {
                google.maps.event.addListener(this.marker, 'mouseout', $.proxy(this.opts.onMouseOut, this));
            }
			
        }
		
    };

}).apply(REWMap || {}, [jQuery, window, document]);