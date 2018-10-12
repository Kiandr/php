(function() {
    'use strict';

    /**
     * Deferred images
     * <img data-src="">
     */
    $('img[data-src]').Images({
        onLoad: false
    });

    /**
     * Popup links
     * <a class="popup" data-popup='{}'></a>
     */
    $('a.popup').on(BREW.events.click, function () {
        var $this = $(this), options = $this.data('popup');
        $.Window($.extend(options, {
            iframe: $this.attr('href')
        }));
        return false;
    });

    /**
     * Save listing to favorites links
     * <a class="action-save" data-save='{ "mls": "", "feed": "" }'></a>
     */
    $('.action-save[data-save]').on('click', function () {
        var $this = $(this), data = $this.data('save');
        $(data.div).Favorite({
            mls: data.mls,
            feed: data.feed,
            onComplete: function (response) {
                if (response.added) $this.addClass('saved');
                if (response.removed) $this.removeClass('saved');
            }
        });
        return false;
    });

    /**
     * Dropdown menu support
     * <a data-menu="#menu-id">Open</a>
     * <div id="menu-id">Menu</div>
     */
    $('a[data-menu]').each(function() {
        var $link = $(this), $menu = $($link.data('menu'));
        if ($menu.length === 0) return;
        $menu.appendTo('body');

        // Hide Menu on Document Click
        $(document).on('click.menu', function (e) {
            $menu.addClass('hidden');
            $link.removeClass('active');
        });

        // Close menu on window resize
        $(window).on('resize.menu', function (e) {
            $menu.addClass('hidden');
            $link.removeClass('active');
        });

        // Toggle Menu
        $link.on('click', function() {
            if (!$menu.hasClass('hidden')) {
                $menu.addClass('hidden');
                $link.removeClass('active');
            } else {
                var offset = $link.offset();
                $menu.css({
                    'position' : 'absolute',
                    'left' : offset.left,
                    'top' : offset.top + $link.outerHeight()
                }).removeClass('hidden');
                $link.addClass('active');
            }
            return false;
        });

    });

})();
