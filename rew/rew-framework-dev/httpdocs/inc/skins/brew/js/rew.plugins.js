/**
 * $.smartresize
 * @link https://github.com/louisremi/jquery-smartresize
 */
(function($) {
    var event = $.event, resizeTimeout;
    event.special['smartresize'] = {
        setup: function () {
            $(this).bind('resize', event.special.smartresize.handler);
        },
        teardown: function() {
            $(this).unbind('resize', event.special.smartresize.handler);
        },
        handler: function (event, execAsap) {
            // Save the context
            var context = this, args = arguments;
            // set correct event type
	        event.type = 'smartresize';
            if (resizeTimeout) clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function () {
                jQuery.event.handle.apply( context, args);
            }, execAsap === 'execAsap' ? 0 : 100);
        }
    };
    $.fn.smartresize = function(fn) {
        return fn ? this.bind('smartresize', fn) : this.trigger('smartresize', ['execAsap'] );
    };
})(jQuery);

/**
 * $.eqHeight
 */
(function() {
    jQuery.fn.eqHeight = function() {
        return this.each(function(i, v) {
            var tallest	= 0;
            var $cols = $(this).children().css('min-height', 0);
            $cols.each(function() {
                var height = $(this).outerHeight();
                if (height > tallest) tallest = height;
            });
            $cols.css('min-height', tallest + 2);
        });
  	};
})();

/**
 * jQuery Plugin to obtain touch gestures from iPhone, iPod Touch and iPad, should also work with Android mobile phones (not tested yet!)
 * Common usage: wipe images (left and right to show the previous or next image)
 * 
 * @author Andreas Waltl, netCU Internetagentur (http://www.netcu.de)
 * @version 1.1.1 (9th December 2010) - fix bug (older IE's had problems)
 * @version 1.1 (1st September 2010) - support wipe up and wipe down
 * @version 1.0 (15th July 2010)
 */
(function($) {
    $.fn.touchwipe = function(settings) {
        var config = {
            min_move_x: 20,
            min_move_y: 20,
            wipeLeft: function(e) { },
            wipeRight: function(e) { },
            wipeUp: function(e) { },
            wipeDown: function(e) { },
            preventDefaultEvents: true
        };
        if (settings) $.extend(config, settings);
        this.each(function() {
            var startX;
            var startY;
            var isMoving = false;
            function cancelTouch () {
                this.removeEventListener('touchmove', onTouchMove);
                startX = null;
                isMoving = false;
            }	
            function onTouchMove(e) {
                if (config.preventDefaultEvents) {
                    e.preventDefault();
                }
                if (isMoving) {
                    var x = e.touches[0].pageX;
                    var y = e.touches[0].pageY;
                    var dx = startX - x;
                    var dy = startY - y;
                    if (Math.abs(dx) >= config.min_move_x) {
                        cancelTouch();
                        if (dx > 0) {
                            config.wipeLeft(e);
                        } else {
                            config.wipeRight(e);
                        }
                    } else if (Math.abs(dy) >= config.min_move_y) {
                        cancelTouch();
                        if (dy > 0) {
                            config.wipeDown(e);
                        } else {
                            config.wipeUp(e);
                        }
                    }
                }
            }
            function onTouchStart(e) {
                if (e.touches.length == 1) {
                    startX = e.touches[0].pageX;
                    startY = e.touches[0].pageY;
                    isMoving = true;
                    this.addEventListener('touchmove', onTouchMove, false);
                }
            }  
            if ('ontouchstart' in document.documentElement) {
                this.addEventListener('touchstart', onTouchStart, false);
            }
        });
        return this;
    };
})(jQuery);
