/************************ BREW.Carousel ************************/

// BREW Carousel
BREW.Carousel = function (el, options) {

    var self = this, $el = $(el);

    // Plugin Options
    var options = $.extend({
        prevBtn		: 'a.prev',         // Selector for Prev Button
        nextBtn		: 'a.next',         // Selector for Next Button
        paginate	: 'dots',           // Pagination Style: false, "dots", or "nums"
        columns		: 0,				// # of Columns
        slideHeight : 'auto', 			// Slide Height
        slideWidth	: '108px',			// Slide Width
        repeat		: false,            // Repeat Slides
        fullscreen	: false,            // Use Fullscreen API (Experimental)
        onHover		: null,             // Hover Callback
        onClick		: function (el) {   // Click Callback
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
    var $row	= $el.find('.slideset').wrap('<div class="wrap"></div>')
        , $imgs	= $row.find('div')
 ;

    // Row Data
    self.row = 0;
    self.rows = 0;

    // Initialize
    self.init = function () {

        // Dimensions
        var rowWidth = 0, maxWidth = $el.width();

        // Set # of Columns
        if (options.columns > 0) {

            // Split Slides into Columns
            var $col = $('<div class="slidesetcol" />').css('float', 'left'), c = options.columns, i = 0 , l = $imgs.length;
            for (i; i < l; i += c) $imgs.slice(i, i + c).wrapAll($col);

            // Calculate Dimensions
            var baseSize				= 800,
			    baseGutter				= 20,
			    numberofCols			= Math.ceil($imgs.length / options.columns);
			    mainContainerPixelW 	= ((baseSize + baseGutter) * numberofCols) - baseGutter, /* this fixes wrapping issue. limit 100 photos. */
			    mainContainerPercentW 	= (mainContainerPixelW / baseSize) * 100,
			    colContainerPixelW		= baseSize,
			    colContainerPercentW	= (colContainerPixelW / mainContainerPixelW) * 100,
			    colContainerPercentG	= (baseGutter / mainContainerPixelW) * 100,
			    slidePixelW				= ((baseSize + baseGutter) / options.columns) - baseGutter,
			    slidePercentW			= (slidePixelW / baseSize) * 100,
			    slidePercentG			= (baseGutter / baseSize) * 100;

            // Sizing + Gutters
            $el.find('.slideset').css('width', mainContainerPercentW + '%');

            // Update Columns
            $el.find('.slidesetcol').css('width', colContainerPercentW + '%')
                .not(':last-child').css('margin-right', colContainerPercentG + '%');

            // Update Slides
            $el.find('.slide').css('width', slidePercentW + '%')
                .not(':last-child').css('margin-right', slidePercentG + '%');

        } else {

            // Set Fixed Dimensions
            if (options.slideHeight && options.slideWidth) {
                $el.find('.slide').css({
                    height : options.slideHeight,
                    width : options.slideWidth
                });
            }

        }

        if (options.columns > 0) {

            // Load Image, Show on Complete
            $el.find('.slidesetcol').first().Images({
                onComplete : function (el) {

                    // Get Tallest Slide
                    var tallest = 0;

                    var display = function () {
                        $el.find('.slidesetcol').first().each(function() {
                            var x = $(this).outerHeight(true);
                            if (x > tallest) tallest = x;
                        });

                        // Set Carousel Height
                        $el.find('.wrap').css('height', tallest).find('.prev,.next').each(function() {
                            var $this = $(this);
                            $this.css('margin-top', '-' + $this.height() / 2);
                        });

                        // Show Carousel, Set Height
                        $el.removeClass('hidden');
                    };

                    // If The DOM Is Loaded, Display The Carousel
                    if (document.readyState == 'complete') {
                        display();
                        // Otherwise Bind It To The Window Load Event
                    } else {
                        $(window).load(display);
                    }

                    // Build Pagination
                    if (options.paginate) self.buildPagination();

                    // Bind Events
                    self.bindEvents();

                }
            });

        } else {

            $row.Images({
                onComplete : function (el) {

                    // Process Images
                    $imgs.each(function() {
                        var $this = $(this), width	= $this.width() + parseInt($this.css('margin-right'));
                        // Increment Widths
                        rowWidth += width;
                        // Split into Row
                        if (rowWidth > maxWidth && width < rowWidth) {
                            rowWidth = width;
                            self.rows++;
                        }
                        // Image Row
                        $this.attr('data-row', self.rows);
                    });

                    // Show Carousel, Set Height
                    $el.removeClass('hidden');

                    // Build Pagination
                    if (options.paginate) self.buildPagination();

                    // Bind Events
                    self.bindEvents();

                }
            });
        }

    };

    // Build Pagination
    self.buildPagination = function () {

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

        // Bind Click Event
        $nav.on(BREW.events.click + '.carousel', 'a', function (e) {
            e.preventDefault();
            var $this = $(this), row = $this.attr('data-row');
            self.setRow(row);
        });

        // Update Pagination
        self.updatePagination();
    };

    // Generate Pagination Links
    self.updatePagination = function () {
        // Build Links
        if (options.columns > 0) self.rows = (Math.ceil($imgs.length / options.columns).toFixed(1))-1;
        var links = [], i = 0;
        for (i; i <= self.rows; i++) {
            links.push('<li' + (self.row == i ? ' class="current"' : '') + '><a href="javascript:void(0);" data-row="' + i + '">' + (i + 1) + '</a></li>');
        }
        // Update Pagination
        var $pagination = $el.find('.pagination');
        $pagination.toggleClass('hidden', i < 2);
        $pagination.html(links.join(''));
    };

    // Set Row
    self.setRow = function (row) {

        // Set # of Columns
        if (options.columns > 0) {
            $el.find('.slideset').css('left', '-' + $el.find('.slidesetcol').eq(row).position().left / $el.find('.wrap').width() * 100 + '%');
            self.row = parseInt(row);

            // Update Pagination
            if (options.paginate) {
                $el.find('.prev, .next').show();
                if (self.row === 0) $el.find('.prev').hide().siblings('a').show();
                if (self.row === $el.find('.slidesetcol').length - 1) $el.find('.next').hide();
                $el.find('a[data-row="' + row + '"]').parent().addClass('current').siblings().removeClass('current');
            }

            // Load The Next Slide's Images
            $el.find('.slideset .slide').filter( function () {
                return ($(this).attr('data-slide') >= self.row * options.columns &&
                        $(this).attr('data-slide') < self.row * options.columns + options.columns);
            }).Images();

            // Classic Carousel
        } else {
            var $img = $imgs.filter('[data-row=' + row + ']:first');
            if ($img.length > 0) {
                var left = $img.position().left;
                if ($.support.transition) {
                    $row.css('left', -left);
                } else {
                    $row.animate({ 'left' : -left });
                }
            }
            if (options.paginate) $el.find('a[data-row="' + row + '"]').parent().addClass('current').siblings().removeClass('current');
            self.row = parseInt(row);

        }

    };

    // Show Prev Row
    self.prev = function () {
        // Go to Prev
        if (self.row > 0) {
            self.setRow(self.row - 1);
            // Go to Last
        } else if (options.repeat) {
            self.setRow(self.rows);
        }
    };

    // Show Next Row
    self.next = function () {
        // Go to Next
        if (self.row < self.rows) {
            self.setRow(self.row + 1);
            // Go to First
        } else if (options.repeat) {
            self.setRow(0);
        }
    };

    // Bind Events
    self.bindEvents = function () {

        // Update on Resize (If Not Responsive)
        if (options.columns > 0) {
            $(window).bind('smartresize.carousel', function () {

                // Dimensions
                var rowWidth = 0, maxWidth = $el.width();

                // Process Images
                self.rows = 0;
                $imgs.each(function() {
                    var $this = $(this), width = $this.width() + parseInt($this.css('margin-right'));
                    // Increment Widths
                    rowWidth += width;
                    // Split into Row
                    if (rowWidth > maxWidth && width < rowWidth) {
                        rowWidth = width;
                        self.rows++;
                    }
                    // Image Row
                    $this.attr('data-row', self.rows);
                });

                // Build Pagination
                if (options.paginate) self.updatePagination();

            });
        }

        // Prev
        $el.on(BREW.events.click + '.carousel', options.prevBtn, function (e) {
            e.preventDefault();
            self.prev();
        });

        // Next
        $el.on(BREW.events.click + '.carousel', options.nextBtn, function (e) {
            e.preventDefault();
            self.next();
        });

        // Image Hover
        $imgs.on('mouseenter.carousel', function (e) {
            if (options.onHover) options.onHover(this);
            e.preventDefault();
        });

        // Image Click
        $imgs.on(BREW.events.click + '.carousel', function (e) {
            if (options.onClick) options.onClick(this);
            //e.preventDefault();
        });

        // Mobile Touch Events
        if (BREW.mobile) {
            $el.touchwipe({
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

// BREW Carousel
$.fn.Carousel = function (options) {

    // Namespace
    var ns = 'BREW.Carousel';

    // Public Methods
    var methods = {
        setRow : function (row) {
            $(this).data(ns).setRow(row);
        },
        prev : function () {
            $(this).data(ns).prev();
        },
        next : function () {
            $(this).data(ns).next();
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
                var plugin = new BREW.Carousel(this, options);
                $this.data(ns, plugin);
            }
        });
    }
};