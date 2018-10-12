(function($, window, document, undefined) {
    'use strict';

    /**
	 * REWMap.MarkerLabel
	 * @param marker google.maps.Marker
	 * @param html
	 */
    this.MarkerLabel = function (marker, text) {
		
        // Label Marker
        this.marker = marker;
		
        // Label Text
        this.text = text;
		
        // Label Element
        var label = this.label = document.createElement('span');
        label.style.cssText = 'position: absolute; display: block; width: 30px; text-align: center; left: -15px; top: -20px; white-space: nowrap; color: #fff; font: bold 11px/11px arial;';

        // Container Element
        var div = this.div = document.createElement('div');
        div.style.cssText = 'position: absolute; display: none';
        div.appendChild(label);
		
        // Construct OverlayView
        google.maps.OverlayView.call(this);
		
        // Extend OverlayView
        REWMap.MarkerLabel.prototype = $.extend(true, REWMap.MarkerLabel.prototype, google.maps.OverlayView.prototype);
		
	    // Add Overlay to Map
	    this.setMap(this.marker.getMap());
		
    };
	
    // Use Prototype...
    this.MarkerLabel.prototype = {
			
        // Get Position
        getPosition : function () {
            return this.marker.getPosition();
        },
	
        // Set Text
        setText : function (text) {
            this.label.innerHTML = this.text = text.toString();
        },
		
        // Draw Overlay
        draw : function () {
			
            // Update Position
            var div = this.div, projection = this.getProjection(), position = projection.fromLatLngToDivPixel(this.getPosition());
            div.style.left = position.x + 'px';
            div.style.top = position.y + 'px';
            div.style.display = 'block';
			 
            // Set Label Text
            this.label.innerHTML = this.text.toString();

        },
		
        // Add Overlay
        onAdd : function () {

            // Append Container Element to DOM
            var pane = this.getPanes().overlayImage;
            pane.appendChild(this.div);
			
            // Place on Top of Marker
            this.div.style.zIndex = this.marker.getZIndex() + 1;

            // Add Listeners
            var self = this;
            this.listeners = [
			    google.maps.event.addListener(this.marker, 'position_changed', function() {
                    self.draw();
                })
            ];
			
        },
		
        // Remove Overlay
        onRemove : function () {
			
            // Remove DOM Elements
            if (this.div) {
                this.div.parentNode.removeChild(this.div);
            }
			
            // Remove Listeners
            if (this.listeners) {
                var i, l;
                for (i = 0, l = this.listeners.length; i < l; ++i) {
                    google.maps.event.removeListener(this.listeners[i]);
                }
            }
			
        }
		
    };

}).apply(REWMap || {}, [jQuery, window, document]);