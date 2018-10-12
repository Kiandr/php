global.REW.Defer = function ($el, callback) {
    var total = $el.length;

    $el.each(function () {
        var $this = $(this);

        // Load config
        var config = $this.data('fw-deferred-img-config') || {};

        // Use thumbs for low resolution screens
        if (REW.breakpoints && config.sizes) {
            var breakpoint = REW.breakpoints.minName || REW.breakpoints.maxName;

            var oldSrc = config.src;
            if (breakpoint && config.sizes[breakpoint]) {
                config.src = config.sizes[breakpoint];

                if (config.style) {
                    for (var field in config.style) {
                        config.style[field] = config.style[field].replace(oldSrc, config.src);
                    }
                }
            }
        }

        if (!config.src && $this.data('src')) {
            config.src = $this.data('src');
        }

        // Already loaded. This isn't really applicable since we usually use background images, but
        // it allows a fallback for old partial BREW.Images-compatibility.
        if (!config.src || $this.attr('src') == config.src) {
            total--;
            $this.addClass('loaded');
        } else {
            var $canvas = $this.parent().addClass('stateLoading');
            var image = new Image();
            $(image).bind('load.images error.images', function () {
                $canvas.removeClass('stateLoading');

                // Update Image
                if (config.style) {
                    $this.css(config.style);
                } else {
                    $this.attr('src', image.src);
                }

                // Image Loaded
                if (!$this.hasClass('loaded')) {
                    $this.addClass('loaded');
                }

                // All Images Loaded
                if (0 === --total && callback) callback($el);
            });
            image.src = config.src;
        }
    });
};
