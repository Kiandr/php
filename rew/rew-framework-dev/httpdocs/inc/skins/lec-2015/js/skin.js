(function() {
    'use strict';

    // Communities CTA
    $('.communities-nav ul').Splitlist();

    // Popup Links
    $('a.popup').on(BREW.events.click, function () {
        var $this = $(this)
            , href = $this.attr('href')
            , options = $this.data('popup')
  ;
        $.Window($.extend(options, {
            iframe: href
        }));
        return false;
    });

    // Load Images
    $('.photo').Images({
        onLoad: false
    });

    // Load deferred images
    $('.defer[data-src]').Images({
        onLoad: false
    });

    // Equal height containers
    var $equal = $('.equal-heights');
    if ($equal.length > 0) {
        $(window).bind('smartresize', function () {
            $equal.eqHeight();
        }).trigger('smartresize');
    }

    // Toggle navigation for small screens
    $('#primary_nav').on('click', '.hamburger', function() {
        $(this).next('div').toggleClass('hidden-sm');
        return false;
    });

    /**
	 * UI interaction for navigation menus
	 */
    var $menus = $('#primary_nav ul:first-child>li>a').each(function () {
        var $link = $(this)
            , $item = $link.closest('li')
            , $menu = $item.find('.menu')
  ;
        if ($menu.length > 0) {
            $link.on({
                // Toggle navigation feature
                'click.navigation': function () {
                    var opened = $link.parent().siblings('.current').length;
                    if (!$menu.hasClass('opened')) {
                        $menus.trigger('navigation.hide', opened);
                        $link.trigger('navigation.show', opened);
                    } else {
                        $link.trigger('navigation.hide', opened);
                    }
                    return false;
                },
                // Show navigation feature
                'navigation.show': function (e, nofade) {
                    $item.addClass('current').siblings().removeClass('current');
                    if (nofade) {
                        $menu.show().addClass('opened');
                    } else {
                        $menu.fadeIn('fast').addClass('opened');
                    }

                    // Focus on search form
                    $menu.find('.form input[placeholder]').trigger('focus');

                    // Hide navigation menus on <body> click
                    $('body').on('click.navigation', function (e) {
                        var $target = $(e.target);
                        if ($target.is('.menu') || $target.closest('.menu').length > 0) return;
                        $menus.trigger('navigation.hide');
                    });

                },
                // Hide navigation feature
                'navigation.hide': function (e, nofade) {
                    $item.removeClass('current');
                    if (nofade) {
                        $menu.hide().removeClass('opened');
                    } else {
                        $menu.fadeOut('fast').removeClass('opened');
                    }
                    $('body').off('click.navigation');
                }
            });
        }
    });

    /**
	 * UI interaction for <a data-dialog> links
	 */
    var $dialogs = $('a[data-dialog]').each(function () {
        var $link = $(this)
            , dialog = $link.data('dialog')
            , $dialog = $(dialog).addClass('dialog')
  ;
        if ($dialog.length > 0) {
            $link.on({
                // Toggle dialog
                'click.dialog': function () {
                    if ($dialog.hasClass('hidden')) {
                        $dialogs.trigger('dialog.close');
                        $link.trigger('dialog.open');
                    } else {
                        $link.trigger('dialog.close');
                    }
                },
                // Open dialog
                'dialog.open': function () {

                    // Position dialog and show dialog
                    $link.trigger('dialog.position');
                    $dialog.removeClass('hidden');

                    // Close dialogs on window re-size
                    $(window).on('smartresize.dialog', function () {
                        $dialogs.trigger('dialog.close');
                    });

                    // Close dialogs on out of focus click
                    $('body').on('click.dialog', function (e) {
                        var $target = $(e.target);
                        if ($target.is('.dialog') || $target.closest('.dialog').length > 0) return;
                        if ($target.is('a[data-dialog]') || $target.closest('a[data-dialog]').length > 0) return;
                        $dialogs.trigger('dialog.close');
                    });

                },
                // Close dialog
                'dialog.close': function () {
                    $dialog.addClass('hidden');
                    $('body').off('click.dialog');
                    $(window).off('smartresize.dialog');
                },
                // Position dialog
                'dialog.position': function () {
                    var offset = $link.offset();
                    $dialog.css({
                        'top' : offset.top + $link.outerHeight(),
                        'left' : offset.left + $link.outerWidth() - $dialog.outerWidth()
                    });
                }
            });

            // Close link for dialogs
            $dialog.on('click.dialog', 'a[data-dialog-close]', function () {
                $link.trigger('dialog.close');
            });

        }
    });

})();
