(function($, window, document, undefined) {
    'use strict';
	
    /**
	 * REWMap.SearchControl
	 */
    this.SearchControl = function (options) {
		
        // Control Options
        this.opts = $.extend({
            el: null,											// Control Container
            onDraw: null,										// function (search)
            onDelete: null,										// function (search)
            onRefresh: null,									// Refresh Callback
            controls: null,										// google.maps.drawing.DrawingManager (Required)
            multiples : false,									// Allow Multiples
            drawText: 'Draw New',								// Draw Text
            doneText: 'Done Drawing',							// Done Text
            editText: 'Edit',									// Edit Text
            deleteText: '&times;',								// Delete Text
            helpText: 'Click on the map to continue drawing.'	// Tooltip Text
        }, options);
		
        // Draw Mode
        this.MODE_DRAW = 'draw';

        // Idle Mode
        this.MODE_IDLE = 'idle';

        // Set Mode
        this.mode = this.MODE_IDLE;
		
        // Drawing Controls
        this.controls = this.opts.controls ? this.opts.controls : null;
		
        // Active Searches
        this.searches = [];

        // Listener
        this.listener = null;
		
        // Container
        var $el = this.opts.el ? $(this.opts.el) : false;
        if ($el.length > 0) {
		
            // Draw Link
            this.$draw = $('<a href="javascript:void(0);"><span class="ico"></span> <span class="text">' + this.opts.drawText + '</span></a>').appendTo($el);
			
            // Tooltip
            this.$tooltip = $('<small class="tip hidden">' + this.opts.helpText + '</small>').appendTo($el);
			
            // List
            this.$list = $('<ul class="hidden">').appendTo($el);
			
            // Bind Events
            this.bindEvents();
			
        }
		
    };
	
    // Use Prototype...
    this.SearchControl.prototype = {
			
        // Get Searches
        getSearches : function () {
            return this.searches;
        },
		
        // Has Searches
        hasSearches : function () {
            return this.searches.length > 0 ? true : false;
        },
		
        // Enable Control
        enable : function () {
            this.mode = this.MODE_DRAW;
            if (this.$draw) this.$draw.find('.text').text(this.opts.doneText);
            if (this.$tooltip) this.$tooltip.removeClass('hidden');
            this.draw();
            this.refresh();
        },
		
        // Disable Controls
        disable : function () {
            this.mode = this.MODE_IDLE;
            this.controls.setDrawingMode(null);
            if (this.$draw) this.$draw.find('.text').text(this.opts.drawText);
            if (this.$tooltip) this.$tooltip.addClass('hidden');
            google.maps.event.removeListener(this.listener);
            this.refresh();
        },
		
        // Clear Searches
        clear : function () {
            var l = this.searches.length;
            if (l > 0) {
                var i = 0;
                for (i; i < l; i++) {
                    this.searches[i].setMap(null);
                }
            }
            this.searches = [];
            this.refresh();
        },
		
        // Refresh List
        refresh : function () {
            var l = this.searches.length;
            if (this.$list) {
                if (l > 0) {
					
                    // Only One Search Allowed
                    if (this.opts.multiples !== true) {
                        this.$draw.addClass('hidden');
                    }
					
                    // Draw Search List
                    var i = 0, html = '';
                    for (i; i < l; i++) {
                        var search = this.searches[i]/*, size = this.computeSize(search), title = (parseFloat(size) / 1609).toFixed(1) + ' miles&sup2;'*/;
                        search.setEditable(false);
                        html += '<li data-id="' + i + '"><span class="ico"></span> '
       + '<a href="javascript:void(0);" class="edit" data-id=' + i + '>' + this.opts.editText + '</a> '
       + '<a href="javascript:void(0);" class="delete" data-id=' + i + '>' + this.opts.deleteText + '</a></li>';
                    }
                    this.$list.html(html).removeClass('hidden');
					
                } else {
					
                    // Show Draw Link
                    this.$draw.removeClass('hidden');
					
                    // Empty Search List
                    this.$list.addClass('hidden').html('');
					
                }
            }
            if (typeof this.opts.onRefresh === 'function') this.opts.onRefresh.call(this);
        },
		
        // Bind Events
        bindEvents : function () {
			
            // Draw Search
            this.$draw.on('click', $.proxy(function () {
                if (this.mode === this.MODE_IDLE) {
                    this.enable();
                } else {
                    this.disable();
                }
            }, this));
			
            // Edit Search
            this.$list.on('click', 'a.edit', $.proxy(function (event) {
                var $link = $(event.target), id = $link.data('id'), search = this.searches[id], editable = search.getEditable();
                this.disable();
                $link = this.$list.find('a.edit[data-id="' + id + '"]');
                if (editable) {
                    search.setEditable(false);
                    $link.text(this.opts.editText);
                    // Fire onDraw Call
                    if (typeof this.opts.onDraw === 'function') this.opts.onDraw.call(this, search);
                } else {
                    search.setEditable(true);
                    $link.text('Done');
                }
            }, this));
			
            // Delete Search
            this.$list.on('click', 'a.delete', $.proxy(function (event) {
                var $link = $(event.target), id = $link.data('id'), search = this.searches[id];
                if (search) {
                    search.setMap(null);
                    this.searches.splice (id, 1);
                    this.refresh();
                    if (typeof this.opts.onDelete === 'function') this.opts.onDelete.call(this, search);
                }
            }, this));
			
            // Mouse Events
            this.$list.on({
                mouseenter : $.proxy(function (event) {
                    var $link = $(event.target), id = $link.data('id'), search = this.searches[id];
                    if (search) search.setOptions({fillOpacity: 0.50});
                }, this),
                mouseleave :  $.proxy(function (event) {
                    var $link = $(event.target), id = $link.data('id'), search = this.searches[id];
                    if (search) search.setOptions({fillOpacity: 0.25});
                }, this)
            }, 'li');
			
        }
		
    };

}).apply(REWMap || {}, [jQuery, window, document]);