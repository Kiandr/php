/* global BREW, jQuery, window, document */
(function (BREW, $, window, document, undefined) {
    'use strict';

    // BREW Window
    BREW.Window = function (el, options) {
        var self = this, $el = $(el);

        // Plugin Options
        options = $.extend({
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
        }, BREW.Window.UI, options);

        // Window Overlay
        var $overlay = $('<div class="overlay" />');

        // Initialize
        self.init = function () {

            // Initialized
            $el.addClass('init');
            if (options.width) $el.css('width', options.width);
            if (options.height) $el.css('height', options.height).find('.' + BREW.Window.UI.paneClass).css('max-height', options.height);

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
            $overlay.appendTo(document.body).show();
            $('body').on('touchmove.window', function (e) {
                var $t = $(e.target);
                if ($t.closest('.' + BREW.Window.UI.paneClass).length > 0) {
                    return true;
                }
                e.preventDefault();
            });
        };

        // Remove Overlay
        self.removeOverlay = function () {
            $overlay.remove();
            $('body').off('touchmove.window');
        };

        // Hide Overlay
        self.hideOverlay = function () {
            $overlay.hide();
        };

        // Get Title
        self.getTitle = function () {
            return $el.find('header').find('.' + BREW.Window.UI.titleClass).html();
        };

        // Get Contents
        self.getContents = function () {
            return $el.find('.' + BREW.Window.UI.paneClass).html();
        };

        // Set Title
        self.setTitle = function (title) {
            $el.find('header').find('.' + BREW.Window.UI.titleClass).html(title);
        };

        // Set Contents
        self.setContents = function (contents) {
            $el.find('.' + BREW.Window.UI.paneClass).html(contents);
        };

        // Update Position
        self.position = function () {

            // Set <iframe> height
            var iFrame = false;
            if (options.iframe) {
                var $iframe = $el.find('iframe');
                if ($iframe.length > 0) {
                    $iframe.trigger('adjustHeight');
                    iFrame = true;
                }
            }

            // Max Height
            var maxHeight = $(window).height();

            // Original Height
            var origHeight = $el.outerHeight();

            // Check Height
            var height = maxHeight > 0 && origHeight > maxHeight ? maxHeight : origHeight;

            // Set max height based on window
            if (!iFrame) {
                var $header = $el.find('header')
                    , headerh = $header.length === 1 ? $header.outerHeight() : 0
                    , actualh = $el.height()
                    , paneh = actualh < maxHeight ? actualh - headerh : maxHeight - headerh
                ;
                $el.find('.' + BREW.Window.UI.paneClass).css('max-height', paneh);
            }

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
            if ($el.hasClass('closed')) {
                // Close Other Windows
                $('.window.init:not(.stacked)').not($el).Window('close');
                // Position Window
                self.position();
                // Show Window
                $el.removeClass('closed');
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
            $('body').removeClass('mute');
            if (options.iframe || options.ajax || options.content) $el.remove();
            if (options.overlay) self.removeOverlay();
            if (options.onClose) options.onClose(this);
        };

        // Hide Window
        self.hide = function () {
            $el.addClass('closed').hide();
            $('body').removeClass('mute');
            if (options.overlay) self.hideOverlay();
            if (options.onClose) options.onClose(this);
        };

        // Bind Events
        self.bindEvents = function () {

            // Close Window If Clicked Outside
            window.setTimeout(function () {
                $(document).on(BREW.events.click + '.window', function (e) {
                    var $t = $(e.target).closest('.window');
                    if ($t.get(0) != el && options.closeOnClick) {
                        $(document).off(BREW.events.click + '.window');
                        self.close();
                    }
                });
            }, 2000);

            // Close Window If "Esc" Key is Pressed
            $(document).on('keyup.window', function (e) {
                options.closeOnEscape && e.which == 27 && self.close();
            });

            // Close Window
            if (options.closeBtn) {
                $el.on(BREW.events.click + '.window', 'header .' + BREW.Window.UI.buttonSetClass, function (e) {
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

    // BREW Window
    $.Window = $.fn.Window = function (options) {
        $.extend(options, BREW.Window.UI);

        // Namespace
        var ns = 'BREW.Window';

        // 'No Close'
        if (options.noClose) {
            options = $.extend({
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
            },
            hide : function () {
                this.hide();
            }
        };

        // Create Window
        var createWindow = function (opts) {
            var windowClass = opts.windowClass + ' closed';
            if (opts.iframe) windowClass += ' iframe';
            var headerClass = opts.headerClass;
            var titleClass = opts.titleClass;
            var paneClass = opts.paneClass;
            var content = opts.content || '';
            var titleText = opts.title || (opts.ajax || opts.iframe ? 'Loading&hellip;' : '&nbsp;');
            var buttonSetClass = opts.buttonSetClass;
            var closeButtonHtml = opts.closeButtonHtml;
            return $('<div class="' + windowClass + '">'
                + '<header class="' + headerClass + '">'
                    + '<h4 class="' + titleClass + '">' + titleText + '</h4>'
                    + (opts.closeBtn !== false ? '<div class="' + buttonSetClass + '">' + closeButtonHtml + '</div>' : '')
                + '</header>'
                + '<div class="' + paneClass + '">' + content + '</div>'
            + '</div>');
        };

        // Dynamic Window
        if (!(this instanceof $) && typeof(options) != 'string') {

            // Load via AJAX
            if (options.ajax) {
                $.ajax({
                    url : options.ajax,
                    type : 'get',
                    dataType : 'html',
                    success : function (data) {
                        var $html = $(data);
                        var title = $html.filter('.' + BREW.Window.UI.titleClass).text();
                        var html = $html.find('#content').html();
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
                options = $.extend(options, {
                    width: 800, open: false,
                    content : '<iframe src="' + options.iframe + '" width="100%" frameborder="0"></iframe>'
                }), $el = createWindow(options);

                // Load Callback
                $el.find('iframe').on('load', function () {

                    // Iframe title
                    var doc = this.contentWindow.document
                        , docTitle = doc.title
                        , $body = $('body', doc)
                    ;

                    // Open Window
                    $el.Window('open');

                    // Set Title, Position, Open
                    window.setTimeout(function () {

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
                        }, 2000);

                // Adjust <iframe> height
                }).on('adjustHeight', function () {

                    // Iframe Detials
                    var doc = this.contentWindow.document
                        , $body = $('body', doc)
                        , height = $body.height()
                        , headerh = $el.find('header').outerHeight()
                        , wh = $(window).height()
                        , bodyh = 0
                        , popuph = 0
                    ;

                    wh = (wh < 50) ? wh : (wh - 50);

                    if ((height + headerh) < wh) {
                        bodyh = height;
                        popuph = height + headerh;
                    } else {
                        bodyh = wh - headerh;
                        popuph = wh;
                    }

                    // Set Height
                    $(this).height(bodyh);
                    $body.height(bodyh);
                    $el.height(popuph);

                });

                // Create Window
                var $window = $el.Window(options);

                // Show Overlay
                $window.Window('addOverlay');
                $('body').addClass('mute');

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

    // UI Configuration
    BREW.Window.UI = {
        windowClass: 'window',
        headerClass: 'header',
        titleClass: 'title',
        paneClass: 'pane',
        buttonSetClass: 'btnset',
        closeButtonHtml: '<a class="close btn"><i class="icon-remove"></i></a>',
    };

}).apply({}, [BREW, jQuery, window, document]);
