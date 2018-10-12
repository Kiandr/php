/**
 * REWMap API
 */
var REWMap = (function($, window, document, undefined) {
    'use strict';
	
    /**
	 * $.REWMap
	 */
    var ns = 'REWMap';
    $.fn[ns] = function(options) {
        if (typeof arguments[0] === 'string') {
            var method = arguments[0], args = Array.prototype.slice.call(arguments, 1), value;
            this.each(function() {
                var inst = $.data(this, ns);
                if (inst && typeof inst[method] === 'function') {
                    value = inst[method].apply(inst, args);
                }
            });
            return value;
        } else if (typeof options === 'object' || !options) {
            return this.each(function() {
                $.data(this, ns, new REWMap(this, options));
            });
        }
    };
	
    /**
	 * REWMap
	 */
    var REWMap = function (el, options) {
		
        // Map Container
        this.$el = $(el);
		
        // Map Options
        this.opts = $.extend(true, {
            init: true,      					// Init on Load
            onInit : null,						// Init Callback
            tooltip: true,   					// Enable Tooltips
            streetview: false,					// Enable Streetview
            birdseye: false,                    // Display Birdseye
            scrollwheel: false,					// Enable scrollwheel zooming
            restore: false,   					// Restore Map State (Center & Zoom Level)
            type: 'roadmap',					// Map Type
            zoom: 12,							// Zoom Level
            maxZoom: 18,						// Max Zoom Level
            center : {},						// Center Point {lat : 0, lng : 0}
            polygons: [],    					// Polygon Searches
            radiuses: [],    					// Radius Searches
            manager : {},						// Marker Manager Options, @see REWMap.MarkerManager
            polygonControl : {					// Polygon Control
                el : '#GPolygonControl',
                onRefresh : null,
                polygonOptions : {
                    fillColor: '#0055FF',
                    fillOpacity: 0.25,
                    strokeColor: '#0000FF',
                    strokeOpacity: 0.5,
                    strokeWeight: 2
                }
            },
            radiusControl : {					// Radius Control
                el : '#GRadiusControl',
                onRefresh : null,
                circleOptions : {
                    fillColor: '#0EBE09',
                    fillOpacity: 0.25,
                    strokeColor: '#0EBE09',
                    strokeOpacity: 1,
                    strokeWeight: 2
                }
            },
            mapOptions : {}
        }, options);
		
        // Map Overlays
        this.overlays = [];
		
        // Map Tooltip
        var tooltip = this.opts.tooltip;
        if (typeof tooltip !== 'undefined') {
            this.tooltip = new REWMap.Tooltip(
                typeof tooltip === 'object'
                    ? tooltip
                    : {}
            );
        }
		
        // Initialize
        if (this.opts.init) this.init();
		
    };
	
    // API Version @see https://developers.google.com/maps/documentation/javascript/basics#Versioning
    REWMap.version = 3;
	
    // API Loaded
    REWMap.isLoaded = false;

    // Mobile Client Detected
    REWMap.isMobile = navigator.userAgent.match(/(iPhone|iPod|iPad|Android|BlackBerry)/) ? true : false;
	
    // Map Libraries
    REWMap.libraries = ['drawing', 'geometry'];

    // Load Google Maps API
    REWMap.loadApi = function (callback) {
        if (!GOOGLE_API_KEY) return;

        // Already Loaded
        if (REWMap.isLoaded === true) {
            if (typeof callback === 'function') callback();
		
            // Currently Loading...
        } else if (typeof REWMap.callback === 'function' && typeof callback === 'function') {
            var cb = REWMap.callback;
            REWMap.callback = function () {
                cb();
                callback();
            };
		
            // Start Loading...
        } else {
			
            // Set Callback
            REWMap.callback = function () {
                REWMap.isLoaded = true;
                if (typeof callback === 'function') callback();
            };
			
            // Load Google Maps v3 API
            var script = document.createElement('script');
            script.src = '//maps.googleapis.com/maps/api/js?v=' + REWMap.version
    + (REWMap.libraries ? '&libraries=' + REWMap.libraries.join(',') : '')
    + (REWMap.callback ? '&callback=REWMap.callback' : '')
    + '&key=' + GOOGLE_API_KEY;
            document.body.appendChild(script);
			
        }
		
    };
	
    // Use Prototype...
    REWMap.prototype = {
			
        // Get Self
        getSelf : function () {
            return this;
        },
		
        // Get Google Map
        getMap : function () {
            return this.gmap;
        },
		
        // Get Map Tooltip
        getTooltip : function () {
            return this.tooltip;
        },
		
        // Get Map Type Id
        getTypeId : function (type) {
            switch (type) {
            default :
            case 'roadmap' :
            case google.maps.MapTypeId.ROADMAP :
                return google.maps.MapTypeId.ROADMAP;
            case 'hybrid' :
            case google.maps.MapTypeId.HYBRID :
                return google.maps.MapTypeId.HYBRID;
            case 'terrain' :
            case google.maps.MapTypeId.TERRAIN :
                return google.maps.MapTypeId.TERRAIN;
            case 'satellite' :
            case google.maps.MapTypeId.SATELLITE :
                return google.maps.MapTypeId.SATELLITE;
            }
        },

        // Extend Map Options
        setOptions : function (opts) {
            return $.extend(true, this.opts, opts);
        },
		
        // Show Loader
        showLoader : function () {
            this.loader.setMap(this.gmap);
        },
		
        // Hide Loader
        hideLoader : function () {
            this.loader.setMap(null);	
        },
		
        // Set Map Center
        setCenter : function (lat, lng) {
            this.gmap && this.gmap.setCenter(new google.maps.LatLng(lat, lng));
        },
		
        // Set Map Zoom
        setZoom : function (zoom) {
            this.gmap && this.gmap.setZoom(zoom);
        },
		
        // Set Map Type
        setType : function (type) {
            this.gmap && this.gmap.setMapTypeId(this.getTypeId(type));
        },
		
        // Get Map Polygons
        getPolygons : function () {
            return this.polygonControl && this.polygonControl.serialize();
        },
			
        // Get Map Radiuses
        getRadiuses : function () {
            return this.radiusControl && this.radiusControl.serialize();
        },
		
        // Get Map Bounds
        getBounds : function () {
            return this.gmap && this.gmap.getBounds();
        },
		
        // Get Map Center
        getCenter : function () {
            return this.gmap && this.gmap.getCenter();
        },
		
        // Get Map Zoom
        getZoom : function () {
            return this.gmap && this.gmap.getZoom();
        },
		
        // Get Map Type
        getType : function () {
            return this.gmap && this.getTypeId(this.gmap.getMapTypeId());
        },
		
        // Load Map
        init : function () {
			
            REWMap.loadApi($.proxy(function () {

                // Add a method to the Circle prototype
                google.maps.Circle.prototype.contains = function(LatLng) {
                    return this.getBounds().contains(LatLng) && google.maps.geometry.spherical.computeDistanceBetween(this.getCenter(), LatLng) <= this.getRadius();
                };
				
                // Restore Map State
                var center = null, zoom = null;
                if (this.opts.restore !== false) {
                    var state = this.getState('map.state');
                    if (state) {
                        state = state.split(',');
                        if (state[0] && state[1]) {
                            center = {
                                lat : state[0],
                                lng : state[1]
                            };
                        }
                        if (state[2]) {
                            zoom = parseInt(state[2]);
                        }
                    }
                }
				
                // Restore Last Centerpoint
                center = center ? center : this.opts.center;
				
                // Restore Last Zoom Level
                zoom = zoom > 0 ? zoom : this.opts.zoom;
				
                // Restore Last Type
                var type = this.getState('map.type');
                // Birdseye map always stays as satellite
                type = type && !this.opts.birdseye ? type : this.getTypeId(this.opts.type);

                // Initialize Map
                this.gmap = new google.maps.Map(this.$el.get(0), $.extend({
                    mapTypeId: type,
                    zoom : zoom,
                    maxZoom : this.opts.maxZoom,
                    scrollwheel : this.opts.scrollwheel,
                    streetViewControl : this.opts.streetview
                }, this.opts.mapOptions));
				
                // Map Loader
                this.loader = new google.maps.Polygon({
                    paths: [[
				        new google.maps.LatLng( 90, -180),
				        new google.maps.LatLng( 90, 0),
				        new google.maps.LatLng(-90, 0),
				        new google.maps.LatLng(-90, -180)
				    ], [
				        new google.maps.LatLng( 90, -180),
				        new google.maps.LatLng( 90, 0.000001),
				        new google.maps.LatLng(-90, 0.000001),
				        new google.maps.LatLng(-90, -180)
				    ]],
			        fillColor: '#000',
			        fillOpacity: 0.6,
			        strokeOpacity: 0,
			        strokeWeight: 0
                });
				
                // Polygon Control
                this.polygonControl = new REWMap.PolygonControl($.extend({
                    controls: new google.maps.drawing.DrawingManager({
                        map: this.gmap,
                        drawingControl: false,
                        drawingControlOptions: {
                            drawingModes: [google.maps.drawing.OverlayType.POLYGON]
                        },
                        polygonOptions: $.extend({
                            editable: true
                        }, this.opts.polygonControl.polygonOptions)
                    })
                }, this.opts.polygonControl));
				
                // Radius Control
                this.radiusControl = new REWMap.RadiusControl($.extend({
                    controls: new google.maps.drawing.DrawingManager({
                        map: this.gmap,
                        drawingControl: false,
                        drawingControlOptions: {
                            drawingModes: [google.maps.drawing.OverlayType.CIRCLE]
                        },
                        circleOptions: $.extend({
                            editable: true
                        }, this.opts.radiusControl.circleOptions)
                    })
                }, this.opts.radiusControl));
				
                // Set Center
                this.setCenter(center.lat, center.lng);
				
                // Marker Manager
                this.manager = new REWMap.MarkerManager($.extend({
                    map: this
                }, this.opts.manager));
				
                // Wait Until Map Is Done Loading...
                google.maps.event.addListenerOnce(this.gmap, 'idle', $.proxy(function() {
					
                    // Cookie path
                    var path = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
					
                    // Map Idle: This event is fired when the map becomes idle after panning or zooming.
                    google.maps.event.addListener(this.gmap, 'idle', $.proxy(function () {
						
                        // Hide Tooltips
                        if (this.tooltip) this.tooltip.hide(true);
						
                        // Save Map State
                        if (this.opts.restore !== false) {
                            var center = this.getCenter();
                            this.saveState('map.state', center.lat() + ',' + center.lng() + ',' + this.getZoom(), {
                                path: path
                            });
                        }
						
                    }, this));
					
                    // Map Type Changed
                    google.maps.event.addListener(this.gmap, 'maptypeid_changed', $.proxy(function () {
                        this.saveState('map.type', this.gmap.getMapTypeId(), {
                            path : path
                        });
                    }, this));
					
                    // Draw Polygons
                    if (this.opts.polygons) this.polygonControl.load(this.opts.polygons);
					
                    // Draw Radiuses
                    if (this.opts.radiuses) this.radiusControl.load(this.opts.radiuses);
					
                    // Init Callback
                    if (typeof this.opts.onInit === 'function') this.opts.onInit.call(this);
					
                }, this));
				
            }, this));
				
        },
		
        // Save Map State
        saveState : function (key, value, options) {
            // SET Cookie
            options = $.extend({}, options);
            if (value === null || value === undefined) {
                options.expires = -1;
            }
            if (typeof options.expires === 'number') {
                var days = options.expires, t = options.expires = new Date();
                t.setDate(t.getDate() + days);
            }
            value = String(value);
            return (document.cookie = [
                encodeURIComponent(key), '=', options.raw ? value : encodeURIComponent(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                options.path	? '; path=' + options.path : '',
                options.domain  ? '; domain=' + options.domain : '',
                options.secure  ? '; secure' : ''
            ].join(''));
        },
		
        // Get Map State from Cookie
        getState : function (key, options) {
			
            // GET Cookie
            options = $.extend({}, options);
            var decode = options.raw ? function(s) { return s; } : decodeURIComponent;
            var pairs = document.cookie.split('; ');
            for (var i = 0, pair; pair = pairs[i] && pairs[i].split('='); i++) {
                if (decode(pair[0]) === key) return decode(pair[1] || ''); // IE saves cookies with empty string as "c; ", e.g. without "=" as opposed to EOMB, thus pair[1] may be undefined
            }
            return null;
			
        },
		
        // Clear Map
        clear : function () {
            this.manager && this.manager.clear();
        },
		
        // Clear Map Polygons
        clearPolygons : function () {
            this.opts.polygons = [];
            this.polygonControl && this.polygonControl.clear();
        },
		
        // Clear Map Radiuses
        clearRadiuses : function () {
            this.opts.radiuses = [];
            this.radiusControl && this.radiusControl.clear();
        },
		
        // Plot Markers
        plot : function () {
            this.manager && this.manager.plot();
        },
		
        // Load Markers
        load : function (markers) {
            this.manager && this.manager.load(markers);
        },
		
        // Check If Map Is Hidden
        isHidden : function () {
            return this.$el.hasClass('hidden') ? true : false;
        },
				
        // Show Map
        show : function (callback) {
            if (this.isHidden()) {
                this.$el.hide().removeClass('hidden').slideDown($.proxy(function () {
                    if (!this.gmap) this.init(); // Init
                    if (this.gmap) google.maps.event.trigger(this.gmap, 'resize'); // Resize
                    if (typeof callback === 'function') callback(); // Callback
                }, this));
            } else {
                if (!this.gmap) this.init(); // Init
                if (this.gmap) google.maps.event.trigger(this.gmap, 'resize');
                if (typeof callback === 'function') callback();
            }
        },
		
        // Hide Map
        hide : function (callback) {
            if (!this.isHidden()) {
                if (this.tooltip) this.tooltip.hide(true);
                this.$el.slideUp($.proxy(function () {
                    this.$el.addClass('hidden');
                    if (this.gmap) google.maps.event.trigger(this.gmap, 'resize'); // Resize
                    if (typeof callback === 'function') callback(); // Callback
                }, this));
            } else {
                if (typeof callback === 'function') callback();
            }
        }
	
    };
	
    // Return Map
    return REWMap;

}).apply({}, [jQuery, window, document]);