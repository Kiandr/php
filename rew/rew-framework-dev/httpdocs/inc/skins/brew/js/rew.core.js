/**
 * BREW Framework
 *  - BREW.Cookie
 *  - BREW.Splitlist
 *  - BREW.Resize
 *  - BREW.Images
 *  - BREW.Gallery
 *  - BREW.Carousel
 */
// BREW
var BREW = {
    mobile : navigator.userAgent.match(/(iPhone|iPod|iPad|Android|BlackBerry)/) ? true : false,
    events : {
        click : 'click'
    }
};

/************************ BREW.Cookie ************************/

// BREW Cookie
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

/************************ BREW.Splitlist ************************/

// BREW Splitlist
BREW.Splitlist = function (el, options) {

    // Plugin Options
    var options = $.extend({
        columns : 3
    }, options);

    // Plugin Vars
    var $el = $(el),
        items = $el.children(),
        tag = $el.get(0).tagName.toLowerCase(),
        rows = Math.ceil(items.length / options.columns);

    // Split List
    var $list = $el,
        itemsCount = 0,
        itemsAdded = 0;
    for (var i = 1; i <= options.columns; i++) {
        var $new = $(document.createElement(tag));
        for (var j = itemsCount; j < itemsCount + rows; j++) {
        	$new.append(items[j]);
            itemsAdded++;
        }
        itemsCount = itemsAdded;
        rows = Math.ceil((items.length - itemsAdded) / (options.columns - i));
        $new.attr('class', $list.attr('class'));
        $list.after($new);
        $list = $new;
    }
    // Remove Original Nav Container
    $el.remove();

};

// BREW Splitlist
$.fn.Splitlist = function (options) {
    return this.each(function () {
        (new BREW.Splitlist(this, options));
    });
};

/************************ BREW.Resize ************************/

// BREW Resize
BREW.Resize = function (el, options) {
    var self = this, $el = $(el);

    // Plugin Vars
    var self = this
        , $el = $(el)
        , $canvas = $el.parent()
 ;

    // Base the canvas on the parent container div, not the picture element itself if present
    if ($canvas.is('picture')) $canvas = $canvas.parent();

    // Plugin Options
    var options = $.extend({
        method		: 'scale', // Resize Method: "crop" or "scale"
        ratio		: '3:4',
        className	: 'brewImage',
        bgColor		: 'transparent'
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
            $el
                .removeClass(landscape ? 'portrait' : 'landscape')
                .addClass(landscape ? 'landscape' : 'portrait')
                .addClass(options.method)
            ;

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
            $el.on('load', function () {
                resizeMe();
            }).trigger('load');
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
    if (total === 0 && options.onComplete) options.onComplete($imgs);
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

/************************ BREW.Menu ************************/

// BREW Menu
BREW.Menu = function (el, options) {

    var self = this, $el = $(el);

    // Plugin Options
    var options = $.extend({
        onOpen : null,  // Open Callback
        onClose : null,  // Close Callback
        onClick : null  // Close Callback
    }, options);

    // Plugin Vars
    var $btn = $el.find('>.btn'), $menu = $el.find('.menu');

    // Initialize
    self.init = function () {
        // Bind Events
        self.bindEvents();
    };

    // Open Menu
    self.open = function () {
        // Show Menu
        $menu.removeClass('hidden');
        // Execute Callback
        if (options.onOpen) options.onOpen(this);
    };

    // Close Menu
    self.close = function () {
        // Hide Menu
        $menu.addClass('hidden');
        // Execute Callback
        if (options.onClose) options.onClose(this);
    };

    // Toggle Menu
    self.toggle = function (e) {
        e.preventDefault();
        if ($menu.hasClass('hidden')) {
            self.open();
        } else {
            self.close();
        }
    };

    // Bind Events
    self.bindEvents = function () {

        // Close Menu If Clicked Outside
        $(document).bind(BREW.events.click + '.menu', function (e) {
            var $t = $(e.target).closest('.trigger');
            if ($t.get(0) != el) {
                self.close();
            }
        });

        // Open Menu
        $btn.on(BREW.events.click + '.menu', self.toggle);

        // Menu Item Clicked
        $menu.on('li', BREW.events.click + '.menu', function (e) {
            // Execute Callback
            if (options.onClick) options.onClick(this);
            e.preventDefault();
        });

    };

    // Initialize
    self.init();
};

// BREW Menu
$.fn.Menu = function (options) {

    // Namespace
    var ns = 'BREW.Menu';

    // Public Methods
    var methods = {
        open : function () {
            $(this).data(ns).open();
        },
        close : function () {
            $(this).data(ns).close();
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
                var plugin = new BREW.Menu(this, options);
                $this.data(ns, plugin);
            }
        });
    }

};

/************************ BREW.Autocomplete ************************/

// BREW Autocomplete
BREW.Autocomplete = function (el, options) {

    // Plugin Vars
    var self = this
        , $input = $(el).attr('autocomplete', 'off').wrap('<div class="ac-input"></div>')
        , $wrap = $input.closest('.ac-input')
        , opts = $input.data('autocomplete')
 ;

    // Plugin Options
    var options = this.opts = $.extend({
        url : '/idx/inc/php/ajax/json.php',
        limit: 10,
        multiple : false,
        params : {},
        onItemSelect : null
    }, options, opts || {});

    // Initialize
    self.init = function () {

        // Input Events
        $input
            .on('focus.ac-input',    self.onfocus)
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

    self.clear = function () {
        $input.data('ac-query', null);
    };

    // Menu Hover
    self.hover = function () {
        self.setActive($(this));
    };

    // Menu Click
    self.click = function () {
        self.setActive($(this), true);
    };

    // Focus Input
    self.onfocus = function () {
        var val = this.value;
        if (options.multiple) {
            val = val.split(/,\s*/).pop();
        }
        if (val.length > 0) {
            self.open();
        }
    };

    // Select Option
    self.select = function () {
        var $labels = $input.closest('.ac-input').find('.menu label'),
            $active = $labels.filter('.active'),
            $label = $active.length ? $active : $labels.first();
        if ($label.length) {
            var value = $label.attr('data-ac-value');
            if (typeof options.onItemSelect == 'function') {
                value = options.onItemSelect.call(this, $label.get(0), value);
            }
            if (options.multiple) {
	            var terms = $input.val().split(/,\s*/);
	            terms.pop();
	            if (value !== false) {
		            terms.push(value);
		            terms.push('');
	            }
	            $input.val(terms.join(', ')).trigger('focus.ac-input');
            } else if (value !== false) {
                $input.val(value);
            } else {
                $input.val('');
            }
            self.close();
            self.clear();
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

            // Query
        default :
            self.query(this.value);

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
    self.query = function (query) {

        // AC Query
        if (options.multiple) {
            query = query.split(/,\s*/).pop();
        }

        // AC Data
        var params = typeof(options.params) == 'function' ? options.params.call() : options.params
            , data = $.extend(params, { q: query })
  ;

        // Ignore Empty Query
        if (data.q.length < 1) return;

        // Abort if same as last search
        var this_query = JSON.stringify(data)
            , last_query = $input.data('ac-query')
  ;
        if (this_query === last_query) return;
        $input.data('ac-query', this_query);

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
        setOption : function (option, value) {
            var options = $(this).data(ns).opts;
            options[option] = value;
        },
        refresh : function () {
            var $this = $(this)
                , val = $this.val()
                , inst = $this.data(ns)
   ;
            inst.clear();
            inst.query(val);
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
                var plugin = new BREW.Autocomplete(this, options);
                $this.data(ns, plugin);
            }
        });
    }
};

/************************ BREW.Truncate ************************/

// BREW Truncate
BREW.Truncate = function (el, options) {

    // Plugin Options
    var options = $.extend({
        count : 600,
        ending: '...',
        moreText: 'more &raquo;',
        lessText: '&laquo; less '
    }, options);

    // Plugin Vars
    var $el 	  = $(el),
    	fullText  = $el.html(),
    	truncText = $el.html().substring(0, options.count).split(' ').slice(0, -1).join(' ') + options.ending;
    	// get first X characters, sep. into array of words, remove the last full or partial word, join and add ending

    // No Truncating Needed
    if (fullText.length < options.count) {
    	return;
    }

    // Default truncate
    $el.html(truncText + ' <a href="#" class="more">'+options.moreText+'</a>');

    // Setup 'more' link
    $el.find('a.more').live('click', function(){
        $el.html(fullText + ' <a href="#" class="less">'+options.lessText+'</a>').show();
        return false;
    });

    // Setup 'less' link
    $el.find('a.less').live('click', function(){
        $el.html(truncText + ' <a href="#" class="more"">'+options.moreText+'</a>').show();
        return false;
    });
};

// BREW Truncate
$.fn.Truncate = function (options) {
    return this.each(function () {
        (new BREW.Truncate(this, options));
    });
};

/*******************************************************************/