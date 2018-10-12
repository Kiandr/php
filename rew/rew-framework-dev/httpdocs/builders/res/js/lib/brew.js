// Subset of the BREW framework

// BREW
var BREW = {
    mobile : navigator.userAgent.match(/(iPhone|iPod|iPad|Android|BlackBerry)/) ? true : false,
    events : {
        click : 'click'
    }
};

var matched, browser;

jQuery.uaMatch = function( ua ) {
    ua = ua.toLowerCase();

    var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
        /(webkit)[ \/]([\w.]+)/.exec( ua ) ||
        /(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
        /(msie)[\s?]([\w.]+)/.exec( ua ) ||
        /(trident)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
        ua.indexOf('compatible') < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
        [];

    return {
        browser: match[ 1 ] || '',
        version: match[ 2 ] || '0'
    };
};

matched = jQuery.uaMatch( navigator.userAgent );
//IE 11+ fix (Trident)
matched.browser = matched.browser == 'trident' ? 'msie' : matched.browser;
browser = {};

if ( matched.browser ) {
    browser[ matched.browser ] = true;
    browser.version = matched.version;
}

// Chrome is Webkit, but Webkit is also Safari.
if ( browser.chrome ) {
    browser.webkit = true;
} else if ( browser.webkit ) {
    browser.safari = true;
}

jQuery.browser = browser;

/************************ BREW.Resize ************************/

// BREW Resize
BREW.Resize = function (el, options) {
    var self = this, $el = $(el);

    // Plugin Vars
    var self = this
        , $el = $(el)
        , $canvas = $el.parent()
 ;

    // Plugin Options
    var options = $.extend({
        method		: 'scale', // Resize Method: "crop" or "scale"
        ratio		: '3:4',
        className	: 'brewImage',
        bgColor		: '#222'
    }, options);

    // Load [data-resize] Options
    var opts = $el.data('resize') || $canvas.data('resize');
    if (opts) $.extend(options, opts);

    // Calculate Height Percent
    var ratio = options.ratio.split(':')
        , shimHPercent = (ratio[0] / ratio[1]) * 100
 ;

    // Append Shim
    var $shim = $('<div class="shim"/>');
    $shim.css({
        'padding-bottom' : shimHPercent + '%'
    }).insertAfter($el);

    // Update Canvas
    $canvas.addClass(options.className).css({
        'background' : options.bgColor
    });

    // Convert Pixels to Percent
    self.pixelsToPercent = function (chi, par) {
        return ((parseInt(chi, 10) / parseInt(par, 10)) * 100) + '%';
    };

    // Resize Image
    self.resize = function () {

        var resizeMe = function () {

            // Force Image Styles
            $el.css({
                'position'	: 'absolute',
                'max-width'	: '9999em',
                'height'	: 'auto',
                'width'		: 'auto'
            });

            // Image Dimensions
            var imgHeight		= $el.height()
                , imgWidth		= $el.width()
                , imgHPercent	= (imgHeight / imgWidth) * 100
   ;

            // Invalid Height, Throw Error
            if (imgHeight <= 0) {
                throw {
                    message : 'Height Error'
                };
            }

            // Clear Image Styles
            $el.attr('style', '');

            // Landscape vs Portrait
            var landscape = (shimHPercent >= imgHPercent);
            $el.addClass(landscape ? 'landscape' : 'portrait').addClass(options.method);

            // Canvas Dimensions
            var canvasHeight	= $canvas.height()
                , canvasWidth	= $canvas.width()
   ;

            // Crop Image
		    if (options.method === 'crop') {
                if (landscape) {
                    var left = (canvasWidth - $el.width()) / 2;
		            $el.css({ 'left': self.pixelsToPercent(left, canvasWidth), 'top' : 0 });
                } else {
                    var top = ($el.height() - canvasHeight) / 2;
                    $el.css({ 'top': self.pixelsToPercent(top * -1, canvasHeight), 'left' : 0 });
                }

                // Scale Image
		    } else if (options.method === 'scale') {
		    	if (landscape) {
			    	var top = ($el.height() - canvasHeight) / 2;
		            $el.css({ 'top': self.pixelsToPercent(top * -1, canvasHeight), 'left' : 0 });
                } else {
                    var left = (canvasWidth - $el.width()) / 2;
		            $el.css({ 'left': self.pixelsToPercent(left, canvasWidth), 'top' : 0 });
                }
		    }

        };

        try {
            // Resize!
            resizeMe();
        } catch (e) {
            // Failed, Try again in a Second
            setTimeout(function () {
                try {
                    // Resize!
                    resizeMe();
                } catch (e) {
                    // Failed, Get angry.
                }
            }, 1000);
        }

    };

    // Require <img>
    if (el.nodeType === 1 && el.tagName.toLowerCase() === 'img' && el.src !== '') {
        // Hide Image
        $el.addClass('hidden');
        // Resize Image
        self.resize($el);
        // Show Image
        $el.removeClass('hidden');
    }

};

// BREW Resize
$.fn.Resize = function (options) {

    // Namespace
    var ns = 'BREW.Resize';

    // Public Methods
    var methods = {
        resize : function (row) {
            $(this).data(ns).resize;
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
                var plugin = new BREW.Resize(this, options);
                $this.data(ns, plugin);
            }
        });
    }
};

/************************ BREW.Images ************************/
// BREW Images
BREW.Images = function (el, options) {
    var $el = $(el);

    // Plugin Options
    var options = $.extend({
        resize : {
            method : 'scale'
        },
        onComplete : null,       // All Images Loaded Callback
        onLoad : function (el) { // Image Loaded Callback
            var opts = this, $img = $(el);
            // Resize Image
            $img.Resize(opts.resize);
        }
    }, options);

    // Get image parent
    if ($el.is('img')) $el = $el.parent();

    // Find Images
    var $imgs = $el.find('img[data-src]')
    	, imgs = []
    ;
    $imgs.each(function () {
        imgs.push(this);
    });

    // Load Images
    var total = imgs.length, loaded = 0;
    $.each(imgs, function (i, img) {
        var $img = $(img);

        // Already Loaded
        var src = $img.attr('data-src');
        if ($img.attr('src') === src) {
        	if (options.onLoad) options.onLoad(img);
        	if (total === ++loaded && options.onComplete) options.onComplete($imgs);
        	return true;
        }

        // Bind Load Event
    	var $canvas = $img.parent().addClass('stateLoading'), image = new Image();
        $(image).bind('load.images error.images', function (e) {
        	$canvas.removeClass('stateLoading');
        	// Update Image
        	img.src = image.src;
            // Image Loaded
        	if (!$img.hasClass('loaded')) {
        		$img.addClass('loaded');
        		if (e.type === 'load' && options.onLoad) options.onLoad(img);
        	}
        	// All Images Loaded
        	if (total === ++loaded && options.onComplete) options.onComplete($imgs);
        });
        // Swap Image Source
        $img.attr('data-org', img.src);
        image.src = $img.attr('data-src');
    });

};

// BREW Images
$.fn.Images = function (options) {
    return this.each(function () {
        (new BREW.Images(this, options));
    });
};


/************************ BREW.Autocomplete ************************/

// BREW Autocomplete
BREW.Autocomplete = function (el, options) {

    // Plugin Options
    var options = $.extend({
        url : '/idx/inc/php/ajax/json.php',
        limit: 10,
        multiple : false,
        params : {}
    }, options);

    // Plugin Vars
    var self = this,
        $input = $(el).attr('autocomplete', 'off').wrap('<div class="ac-input"></div>'),
        $wrap = $input.closest('.ac-input');

    // Initialize
    self.init = function () {

        // Input Events
        $input
            .on('focus.ac-input',    self.open)
            .on('keypress.ac-input', self.keypress)
            .on('keyup.ac-input',    self.keyup);

        // Menu Events
        $wrap
            .on(BREW.events.click + '.ac-input', '.menu label', self.click)
            .on('mouseenter.ac-input', '.menu label', self.hover);

        // Webkit / IE
        if ($.browser.webkit || $.browser.msie) {
            $input.on('keydown.ac-input', self.keypress);
        }

    };

    // Menu Hover
    self.hover = function () {
        self.setActive($(this));
    };

    // Menu Click
    self.click = function () {
        self.setActive($(this), true);
    };

    // Select Option
    self.select = function () {
        var $labels = $input.closest('.ac-input').find('.menu label'),
            $active = $labels.filter('.active'),
            $label = $active.length ? $active : $labels.first();
        if ($label.length) {
            var value = $label.data('ac-value');
            if (options.multiple) {
	            var terms = $input.val().split(/,\s*/);
	            terms.pop();
	            terms.push(value);
	            terms.push('');
	            $input.val(terms.join(', ')).trigger('focus.ac-input');
            } else {
                $input.val(value);
            }
            self.close();
            $input.data('ac-data', null);
        }
    };

    // AC Menu Is Active
    self.isActive = function () {
        var $menu = $input.closest('.ac-input').find('.menu');
        if ($menu.length) {
            if ($menu.hasClass('hidden')) {
                return false;
            } else {
                return true;
            }
        }
        return false;
    };

    // Set Active Label
    self.setActive = function ($label, select) {
        $label.addClass('active').siblings('label').removeClass('active');
        if (select) self.select();
    };

    // Select Next
    self.next = function () {
        var $labels = $input.closest('.ac-input').find('.menu label'),
            $active = $labels.filter('.active').next('label');
        $label = $active.length ? $active : $labels.first();
        self.setActive($label);
    };

    // Select Previous
    self.prev = function () {
        var $labels = $input.closest('.ac-input').find('.menu label'),
            $active = $labels.filter('.active').prev('label'),
            $label = $active.length ? $active : $labels.last();
        self.setActive($label);
    };

    // Open Menu
    self.open = function () {

        // Show Menu
        $input.siblings('.menu').removeClass('hidden');

        // Close AC Menu on Document Click
        $(document).on(BREW.events.click + '.ac-input', function (e) {
            var $t = $(e.target).closest('.ac-input');
            if ($t.length == 1) return;
            $(document).off(BREW.events.click + '.ac-input');
            self.close();
        });

    };

    // Close Menu
    self.close = function () {
        $input.closest('.ac-input').find('.menu').addClass('hidden');
    };

    // Input Key Press
    self.keypress = function (e) {

        // Key Code
        switch (e.keyCode) {

        // Ignore
        case 9  : // Tab
        case 13 : // Enter
        case 27 : // Escape
            if (self.isActive()) {
                e.preventDefault();
            }
            break;

            // Up Arrow
		 	case 38 :
		 		//if (e.type != 'keydown') break;
		 		e.preventDefault();
		 		self.prev();
            break;

            // Down Arrow
        case 40 :
            //if (e.type != 'keydown') break;
            e.preventDefault();
            self.next();
            break;

        }

        // Prevent
        e.stopPropagation();

    };

    // Input Key Up
    self.keyup = function (e) {

        // Key Code
        switch (e.keyCode) {

        // Ignore
        case 38 : // Up
        case 40 : // Down
        		break;

    		// Escape
        case 27 :
            self.close();
            break;

            // Select
        case 9  : // Tab
        case 13 : // Enter
            if (self.isActive()) {
                self.select();
            }
            break;

            // Query
        default :
            self.query(this.value);

        }

        // Prevent
        e.stopPropagation();
        e.preventDefault();

    };

    // Highlight Match
    self.highlight = function (query, item) {
        var query = query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&');
        return item.replace(new RegExp('(^' + query + ')', 'ig'), function ($1, match) {
            return '<em>' + match + '</em>';
        });
    };

    // Execute Query
    self.query = function (q) {

        // AC Data
        var params = typeof(options.params) == 'function' ? options.params.call() : options.params,
            data = $.extend(params, {
                q : options.multiple ? q.split(/,\s*/).pop() : q
            }), query = data.q;

        // Ignore Empty Query
        if (data.q.length < 1) return;

        // @TODO: Check Last Search Data
        var last = $input.data('ac-data');
        if (last && last.q == data.q && last.search_city && last.search_city.join(',') == data.search_city.join(',')) return;
        $input.data('ac-data', data);

        // Clear Last Search
        var search = $input.data('ac-search');
	    if (search) clearTimeout(search);

	    // AC Search Timeput
	    $input.addClass('loading').data('ac-search', setTimeout(function() {

	        // Clear Last Request
	        var ajax = $input.data('ac-ajax');
	        if (ajax) ajax.abort();

	        // AC AJAX Request
	        $input.data('ac-ajax', $.ajax({
	            'url'  : options.url,
	            'type' : 'get',
	            'data' : $.extend({}, data, {
	            	limit : options.limit,
	            	search : $input.attr('name')
	            }),
	            'dataType' : 'json',
	            'success' : function (data) {
	                if (typeof(data.options) == 'undefined') return;

	                // Remove Old Menu
	                $wrap.find('.menu').remove();

	                // Require Options
	                if (data.options.length > 0) {

		                // Build AC Menu
		                var menu = '<div class="menu toggleset">';
		                $.each(data.options, function () {
		                	menu += '<label data-ac-value="' + this.value + '">' + self.highlight(query, this.title) + '</label>';
		                });
		                menu += '</div>';

		                // Add New Menu
	                	$wrap.append(menu);

		                // Done Loading, Focus on Input
		                $input.removeClass('loading').trigger('focus.ac-input');

	                }

	            }
	        }));

	    }, 250));

    };

    // Initialize
    self.init();

};

// BREW Autocomplete
$.fn.Autocomplete = function (options) {

    // Namespace
    var ns = 'BREW.Autocomplete';

    // Public Methods
    var methods = {
    };

    // Method Call
    if (methods[options]) {
        return methods[options].apply(this, Array.prototype.slice.call(arguments, 1));

        // Create Instance
    } else if (typeof options === 'object' || !options) {
        return this.each(function () {
            var $this = $(this);
            if (undefined == $this.data(ns)) {
                var plugin = new BREW.Autocomplete(this, options);
                $this.data(ns, plugin);
            }
        });
    }
};

/************************ BREW.Window ************************/

//BREW Window
BREW.Window = function (el, options) {
    var self = this, $el = $(el);

    // Plugin Options
    var options = $.extend({
        ajax          : null,     // Load URL via AJAX
        iframe        : null,     // Load URL via IFrame
        header        : true,     // Window Header
        title         : null,     // Window Title
        content       : null,     // Window Content
        open          : true,     // If true, Window will open when created
        overlay       : true,     // If true, Window will use .overlay to block page interaction
        closeOnEscape : true,     // If true, Window will close on "Esc" keypress
        closeOnClick  : false,    // If true, Window will close when clicked outside
        stack         : false,    // If true, Windows will stack on top of each other
        onOpen        : null,     // Open Callback
        onClose       : null,     // Close Callback
        closeBtn      : true,     // Show Close Button
        width         : 'auto',
        height        : 'auto',
        noClose       : false
    }, options);

    // Window Overlay
    var $overlay = $('<div class="overlay" />');

    // Initialize
    self.init = function () {

        // Width / Height Options
        var css = {};
        if (options.width) css.width = options.width;
        if (options.height) css.height = options.height;

        // Initialized
        $el.addClass('init').css(css);

        // Stacked
        if (options.stack) $el.addClass('stacked');

        // Add to DOM
        $el.appendTo('body');

        // Open Window
        if (options.open) self.open();

        // Bind Events
        self.bindEvents();

    };

    // Get Window
    self.getWindow = function () {
        return $el;
    };

    // Add Overlay
    self.addOverlay = function () {
        $overlay.appendTo(document.body);
    };

    // Remove Overlay
    self.removeOverlay = function () {
        $overlay.remove();
    };

    // Get Title
    self.getTitle = function () {
        return $el.find('header .title').html();
    };

    // Get Contents
    self.getContents = function () {
        return $el.find('div.pane').html();
    };

    // Set Title
    self.setTitle = function (title) {
        $el.find('header .title').html(title);
    };

    // Set Contents
    self.setContents = function (contents) {
        $el.find('div.pane').html(contents);
    };

    // Update Position
    self.position = function () {

        // Max Height
        var maxHeight = $(window).height();

        // Original Height
        var origHeight = $el.outerHeight();

        // Check Height
        var height = maxHeight > 0 && origHeight > maxHeight ? maxHeight : origHeight;

        // Animate
        if ($el.hasClass('positioned')) {
            $el.animate({
                'margin-top'	: - (height !== false ? height / 2 : $el.outerHeight() / 2) + 'px',
                'margin-left'	: - ($el.outerWidth()  / 2) + 'px'
            }, 'fast');
        } else {
            $el.css({
                'margin-top'	: - (height !== false ? height / 2 : $el.outerHeight() / 2) + 'px',
                'margin-left'	: - ($el.outerWidth()  / 2) + 'px'
            }).addClass('positioned');
        }

    };

    // Open Window
    self.open = function () {
        if ($el.hasClass('hidden')) {
            // Close Other Windows
            $('.window.init:not(.stacked)').not($el).Window('close');
            // Position Window
            self.position();
            // Show Window
            $el.removeClass('hidden');
            // Add Class to Body
            $('body').addClass('mute');
            // Add Overlay
            if (options.overlay) self.addOverlay();
            // Execute Callback
            if (options.onOpen) options.onOpen(this);
            // Scroll to Top...
            $('html, body').scrollTop(0);
        }
    };

    // Close Window
    self.close = function () {
        $el.remove();
        $('body').removeClass('mute');
        if (options.overlay) self.removeOverlay();
        if (options.onClose) options.onClose(this);
    };

    // Bind Events
    self.bindEvents = function () {

        // Close Window If Clicked Outside
        setTimeout(function () {
            $(document).on(BREW.events.click + '.window', function (e) {
                var $t = $(e.target).closest('.uiWindow');
                $t.get(0) != el && options.closeOnClick && self.close();
                $(document).off(BREW.events.click + '.window');
            });
        }, 100);

        // Close Window If "Esc" Key is Pressed
        $(document).on('keyup.window', function (e) {
            options.closeOnEscape && e.which == 27 && self.close();
        });

        // Close Window
        if (options.closeBtn) {
            $el.on(BREW.events.click + '.window', 'header .btnset', function (e) {
                e.preventDefault();
                self.close();
            });
        }

        // Re-Position on Re-Size
        $(window).bind('smartresize.window', self.position);

    };

    // Initialize
    self.init();

};

//BREW Window
$.Window = $.fn.Window = function (options) {

    // Namespace
    var ns = 'BREW.Window', options = options ? options : {};

    // 'No Close'
    if (options.noClose) {
        var options = $.extend({
            closeBtn: false,
            closeOnClick: false,
            closeOnEscape: false
        }, options);
    }

    // Public Methods
    var methods = {
        setTitle : function (title) {
            this.setTitle(title);
        },
        addOverlay : function () {
            this.addOverlay();
        },
        removeOverlay : function () {
            this.removeOverlay();
        },
        position : function () {
            this.position();
        },
        open : function () {
            this.open();
        },
        close : function () {
            this.close();
        }
    };

    // Create Window
    var createWindow = function (options) {
        return $('<div class="window' + (options.iframe ? ' iframe' : '') + ' hidden">' +
 		(options.header === false ? '' :
       '<header>' +
     '<h4 class="title">' + (options.title ? options.title : (options.ajax || options.iframe ? 'Loading&hellip;' : '&nbsp;')) + '</h4>' +
     (options.closeBtn !== false ? '<div class="btnset"><a class="close btn"><i class="icon-remove"></i></a></div>' : '') +
    '</header>') +
   '<div class="pane">' + (options.content ? options.content : '') + '</div>' +
  '</div>');
    };

    // Dynamic Window
    if (!(this instanceof $) && typeof(options) != 'string') {

 	// Load via AJAX
 	if (options.ajax) {
 		$.ajax({
 			url : options.ajax,
 			type : 'get',
 			dataType : 'html',
 			success : function (html) {
 				var $html = $(html), title = $html.filter('title').text(), html = $html.find('#content').html();
 				options.title = title;
 				options.content = html;
 				var $el = createWindow(options);
 				return $el.Window(options);
 			}
 		});

 	// Load via <Iframe>
 	} else if (options.iframe) {
 		// Append 'popup' to Query String
 		options.iframe = (options.iframe.indexOf('?') != -1 ? options.iframe + '&' : options.iframe + '?') + 'popup';
 		// Setup Iframe
            var options = $.extend(options, {
                    width: 800, open: false,
                    content : '<iframe src="' + options.iframe + '" width="100%" frameborder="0"></iframe>'
                }), $el = createWindow(options);

            // Load Callback
            $el.find('iframe').on('load', function () {

                // Iframe Detials
                var doc = this.contentWindow.document,
                    docTitle = doc.title,
                    $body = $('body', doc),
                    height = $body.height();

                // Check Window Height
                var h = $(window).height() - 50; // Window Height
                height = (h < height) ? h - 10 : height + 10;

                // Set Height
                $(this).height(height);
                $el.height(height + (options.header === false ? 0 : 35));

                // Open Window
                $el.Window('open');

                // Set Title, Position, Open
                setTimeout(function () {

                    // Set Title
                    $el.Window('setTitle', docTitle);

                    // Set Position
                    $el.Window('position');

                    // Append 'popup' to Query String
                    $body.find('a').each(function () {
                        var $this = $(this), href = $this.attr('href'), target = $this.attr('target');
                        if (target != '_blank' && target != '_parent' && href && href.length > 0 && href.indexOf('javascript:') !== 0 && href.indexOf('#') !== 0) {
                            $this.attr('href', (href.indexOf('?') != -1 ? href + '&' : href + '?') + 'popup');
                        }
                    });

                    // Append 'popup' to Query String
                    $body.find('form').each(function () {
                        var $this = $(this), action = $this.attr('action');
                        if (action && action.length > 0) {
                            $this.attr('action', (action.indexOf('?') != -1 ? action + '&' : action + '?') + 'popup');
                        }
                        $this.on('submit', function () {
                            return true;
                        });
                    });

                }, 100);

            });

            // Create Window
            $window = $el.Window(options);

            // Show Overlay
            $window.Window('addOverlay');

            // Scroll to Top...
            $('html, body').scrollTop(0);

            // Return Window
            return $window;

            // Create Window
 	} else {
	    	var $el = createWindow(options);
	    	return $el.Window(options);

 	}

    }

    // Method Call
    if (typeof(options) == 'string' && methods[options]) {
        // Target All Windows
        if (!(this instanceof $)) {
            $('.window.init').each(function () {
                var win = $(this).data(ns);
                if (win) methods[options].apply(win, Array.prototype.slice.call(arguments, 1));
            });
            return;
        }
        // Target Window Window
        var win = $(this).data(ns);
        if (win) return methods[options].apply(win, Array.prototype.slice.call(arguments, 1));

        // Create Instance
    } else if (typeof options === 'object' || !options) {
        return this.each(function () {
            var $this = $(this);
            if (undefined == $this.data(ns)) {
                var plugin = new BREW.Window(this, options);
                $this.data(ns, plugin);
            }
        });
    }
};

/************************ BREW.Cookie ************************/

//BREW Cookie
BREW.Cookie = function(key, value, options) {
    // SET Cookie
    if (arguments.length > 1 && (!/Object/.test(Object.prototype.toString.call(value)) || value === null || value === undefined)) {
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
    }
    // GET Cookie
    options = value || {};
    var decode = options.raw ? function(s) { return s; } : decodeURIComponent;
    var pairs = document.cookie.split('; ');
    for (var i = 0, pair; pair = pairs[i] && pairs[i].split('='); i++) {
        if (decode(pair[0]) === key) return decode(pair[1] || ''); // IE saves cookies with empty string as "c; ", e.g. without "=" as opposed to EOMB, thus pair[1] may be undefined
    }
    return null;
};
