(function($, window, document, undefined) {
    'use strict';
	
    /**
	 * REWMap.Tooltip
	 */
    this.Tooltip = function (options) {
		
        // Tooltip Options
        this.opts = $.extend({
            delay: 500,					// Hide Delay
            sticky: false,				// Sticky Tooltip
            closeBtn: 'a.action-close',	// Close Link
            className: 'map-tooltip',
            parentEl: 'body'
        }, options);
		
        // Tooltip Container
        this.$el = $('<div class="' + this.opts.className + '">').css({
		    border: 'none',
		    //cursor: 'pointer',
		    position: 'absolute',
		    paddingLeft: '0',
		    zIndex: '9000'
        }).on('click', this.opts.closeBtn, $.proxy(function () {
            this.hide(true);
        }, this));
		
        // Bind Events
        this.bindEvents();
		
    };
	
    // Use Prototype...
    this.Tooltip.prototype = {
	
        // Get Sticky
        getSticky : function () {
            return this.opts.sticky;
        },
		
        // Get HTML
        getHtml : function () {
            return this.$el.html();
        },
		
        // Set Sticky
        setSticky : function (sticky) {
            if (sticky) {
                this.opts.sticky = true;
                this.$el.find(this.opts.closeBtn).removeClass('hidden');
            } else {
                this.opts.sticky = false;
                this.$el.find(this.opts.closeBtn).addClass('hidden');
            }
        },

        // Set HTML
        setHtml : function (html) {
            this.$el.html(html);
        },

        // Set Delay (in Milli Seconds)
        setDelay : function (delay) {
            this.opts.delay = delay;
        },

        // Set Position
        setPosition : function (pos) {

            var leftPos = pos.x;
            var windowSize = document.documentElement.getBoundingClientRect();

            // Adjust horizontal positioning
            var scrL = $(window).scrollLeft();
            var winW = $(window).width();
            var tipW = this.$el.width();
            var posW = pos.x - scrL + tipW;
            var fixW = posW - winW;
            if (fixW > 0) pos.x -= fixW;

            /**
             * Check if tooltip will extend beyond viewport
             * 250 === avg tooltip width
             */
            if ((windowSize.width - pos.x) <= 250) leftPos = windowSize.right - 250 + 'px';

            // CSS position
            this.$el.css({
                left: leftPos,
                top: pos.y + 'px'
            });

        },

        // Show Tooltip
        show : function (sticky) {
            if (this.timeout) clearTimeout(this.timeout);
            if (sticky) this.setSticky(true);
            this.timeout = setTimeout($.proxy(function () {
                this.$el.appendTo(this.opts.parentEl);
            }, this), this.opts.delay);
        },

        // Hide Tooltip
        hide : function (force) {
            if (force) {
                this.setSticky(false);
                this.$el.detach();
            } else if (!this.opts.sticky && !this.over) {
                if (this.timeout) clearTimeout(this.timeout);
                this.timeout = setTimeout($.proxy(function () {
                    if (!this.opts.sticky && !this.over) this.$el.detach();
                }, this), this.opts.delay);
            }
        },
		
        // Bind Events
        bindEvents : function () {
			
            // Tooltip Mouse Enter
            this.$el.on('mouseenter', $.proxy(function () {
                this.over = true;
            }, this));
			
            // Tooltip Mouse Leave
            this.$el.on('mouseleave', $.proxy(function () {
                this.over = false;
                this.hide();
            }, this));
			
        }
		
    };

}).apply(REWMap || {}, [jQuery, window, document]);