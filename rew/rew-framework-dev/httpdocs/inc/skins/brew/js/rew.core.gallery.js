/************************ BREW.Gallery ************************/

// BREW Gallery
BREW.Gallery = function (el, options) {
    var self = this, $el = $(el);

    // Plugin Options
    var options = $.extend({
        current  : 0,                // Active Slide
        prevBtn    : 'a.prev',       // Selector for Prev Button
        nextBtn    : 'a.next',       // Selector for Next Button
        playBtn    : 'a.play',       // Selector for Play Button
        stopBtn    : 'a.stop',       // Selector for Stop Button
        toggleBtn  : 'a.toggle',     // Selector for Pause Button
        fullscreen : false,          // Use Fullscreen API (Experimental)
        repeat     : true,           // Repeat Slides
        autoplay   : true,           // Auto-Play
        interval   : 5000,           // Slide Interval
        paginate   : 'dots',         // Pagination Style: false, "dots", or "nums"
        onInit     : null,           // Initialize Callback
        onPlay     : null,           // Play Callback
        onStop     : null,           // Stop Callback
        onChange   : null,           // Play Callback
        onClick    : function (el) { // Click Callback
            // Full Screen API
            if (options.fullscreen) {
                var el = $(el).find('img').get(0);
                if (el.requestFullScreen) {
				  el.requestFullScreen();
                } else if (el.mozRequestFullScreen) {
				  el.mozRequestFullScreen();
                } else if (el.webkitRequestFullScreen) {
				  el.webkitRequestFullScreen();
                }
            }
        }
    }, options);

    // Plugin Vars
    var $slides = $el.find('.slideset').children('.slide');

    // Current Slide
    self.current = options.current;

    // First Run
    self.firstrun = true;

    // Initialize
    self.init = function () {

        // Setup Slides
        var i = $slides.length;
        while (i--) $($slides[i]).addClass('hidden');
        setTimeout(function () {
            $el.addClass('init');
            self.setSlide(self.current);
        }, 0);

        // Bind Events
        self.bindEvents();

        // Build Pagination
        if (options.paginate) self.buildPagination();

        // Initialization Callback
        if (typeof options.onInit == 'function') options.onInit.call(this);
    };

    // Go to Slide
    self.setSlide = function (index, stop) {
        var slide = self.getSlide(index), $slide = $(slide), $last = $slides.not('.hidden');
        // Stop Timer
        if (stop) self.stop();

        var prev = self.getSlide(index - 1), $prev = $(prev);
        var prevprev = self.getSlide(index - 2), $prevprev = $(prevprev);
        var next = self.getSlide(index + 1), $next = $(next);
        var nextnext = self.getSlide(index + 2), $nextnext = $(nextnext);

        // Remove previous/next marker from trailing slides
        $prevprev.removeClass('prev');
        $nextnext.removeClass('next');

        // Add previous and next markers to slides for clean transitions
        $prev.addClass('prev').removeClass('next');
        $next.addClass('next');

        // Already Active
        if (!$slide.hasClass('hidden')) return;
        // Load Image, Show on Complete
        $slide.Images({
            onComplete : $.proxy(function (el) {
                self.current = index;
                $slide.removeClass('hidden').removeClass('next').removeClass('prev').addClass('current');
                if ($last) $last.addClass('hidden').removeClass('current');
                if (options.paginate) $el.find('a[data-page="' + index + '"]').parent().addClass('current').siblings().removeClass('current');
                // First Load, Start Auto-Play
                if (self.firstrun && options.autoplay) {
                    self.firstrun = false;
                    self.play();
                }
                // Trigger Callback
                if (typeof options.onChange == 'function') options.onChange.call(this);
            }, this)
        });
    };

    // Get Slide
    self.getSlide = function (index) {
        var index = index != null ? index : self.current;
        return $slides.get(index);
    };

    // Go to Prev Slide
    self.prev = function () {
        // Circular should be optional
        if (self.current > 0) {
            self.setSlide(self.current - 1);
            // Go to Last
        } else if (options.repeat) {
            self.setSlide($slides.length - 1);
        }
    };

    // Go to Next Slide
    self.next = function () {
        // Next Slide
        if (self.current + 1 < $slides.length) {
            self.setSlide(self.current + 1);
            // Go to First
        } else if (options.repeat) {
            self.setSlide(0);
        }
    };

    // Play Slideshow
    self.play = function () {
        // Clear Interval
        if (self.timeout) clearInterval(self.timeout);
        // Set Interval
        self.timeout = setInterval(self.next, options.interval);
        // Trigger Callback
        if (typeof options.onPlay == 'function') options.onPlay.call(this);
    };

    // Stop Slideshow
    self.stop = function () {
        // Clear Interval
        if (self.timeout) clearInterval(self.timeout);
        // Trigger Callback
        if (typeof options.onStop == 'function') options.onStop.call(this);
    };

    // Build Pagination
    self.buildPagination = function () {

        // Generate Links
        var len = $slides.length, links = [];
        for (i = 0; i < len; i++) {
            links.push('<li' + (self.current == i ? ' class="current"' : '') + '><a href="javascript:void(0);" data-page="' + i + '">' + (i + 1) + '</a></li>');
        }

        // Pagination Type
        var $nav = $el.find('.pagination');
        switch (options.paginate) {
        case 'dots' :
            $nav.addClass('dots');
            break;
        case 'nums' :
            $nav.addClass('nums');
            break;
        }

        // Setup DOM
        $nav.append(links.join(''));

        // Append DOM
        $el.find('.nav').append($nav);

        // Bind Click Event
        $nav.on(BREW.events.click + '.gallery', 'a', function (e) {
            e.preventDefault();
            var $this = $(this), page = $this.attr('data-page');
            self.setSlide(page);
            self.stop();
        });
    };

    // Bind Events
    self.bindEvents = function () {
        // Go to Prev Slide
        $el.on(BREW.events.click + '.gallery', options.prevBtn, function (e) {
            e.preventDefault();
            self.prev();
            self.stop();
        });
        // Go to Next Slide
        $el.on(BREW.events.click + '.gallery', options.nextBtn, function (e) {
            e.preventDefault();
            self.next();
            self.stop();
        });
        // Play Slideshow
        $el.on(BREW.events.click + '.gallery', options.playBtn, function (e) {
            e.preventDefault();
            self.play();
        });
        // Stop Slideshow
        $el.on(BREW.events.click + '.gallery', options.stopBtn, function (e) {
            e.preventDefault();
            self.stop();
        });
        // Toggle Slideshow
        $el.on(BREW.events.click + '.gallery', options.toggleBtn, function (e) {
            e.preventDefault();
            var ico = $(this).find('.ico');
            if (ico.hasClass('icon-pause')) {
                ico.addClass('icon-play').removeClass('icon-pause');
                self.stop();
            } else {
                ico.addClass('icon-pause').removeClass('icon-play');
                self.play();
            }
        });
        // Image Click
        $slides.on(BREW.events.click + '.gallery', function (e) {
            if (options.onClick) options.onClick(this);
            // If Slide is not an Anchor, Prevent Default
            if (this.tagName.toLowerCase() !== 'a' && $(e.target).closest('a').length === 0) {
                e.preventDefault();
            }
        });

        // Mobile Touch Events
        if (BREW.mobile) {
            $el.touchwipe({
                preventDefaultEvents: false,
                wipeLeft : function () {
                    self.next();
                    self.stop();
                },
                wipeRight : function () {
                    self.prev();
                    self.stop();
                }
            });
        }

    };

    // Initialize
    self.init();
};

// BREW Gallery
$.fn.Gallery = function (options) {

    // Namespace
    var ns = 'BREW.Gallery';

    // Public Methods
    var methods = {
        setSlide : function (index, stop) {
            $(this).data(ns).setSlide(index, stop);
        },
        getSlide : function (index) {
            return $(this).data(ns).getSlide(index);
        },
        prev : function () {
            $(this).data(ns).prev();
        },
        next : function () {
            $(this).data(ns).next();
        },
        stop : function () {
            $(this).data(ns).stop();
        },
        play : function () {
            $(this).data(ns).play();
        }
    };

    // Method Call
    if (methods[options]) {
        return methods[options].apply(this, Array.prototype.slice.call(arguments, 1));

        // Create Instance
    } else if (typeof options === 'object' || !options) {
        return this.each(function () {
            var $this = $(this);
            if (undefined == $this.data(ns)) {
                var plugin = new BREW.Gallery(this, options);
                $this.data(ns, plugin);
            }
        });
    }
};