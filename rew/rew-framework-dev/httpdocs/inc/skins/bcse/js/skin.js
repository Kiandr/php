(function() {
    'use strict';

    // Detect touch support
    var isTouch = (('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch);
    if (isTouch) $('body').addClass('is-touch');

    // Improve navigation for touch devices
    var $nav = $('#primary_nav')
        , $subnav = $nav.find('li.top-level ul')
 ;
    $subnav.each(function () {
        var $links = $(this);
        $('<a class="touch-drop" />').on('click', function () {
            if ($links.hasClass('active')) {
                $links.removeClass('active');
                $nav.removeClass('open');
            } else {
                $subnav.removeClass('active');
                $links.addClass('active');
                $nav.addClass('open');
            }
        }).insertBefore(this);
    });

    // Toggle search form
    $('.search-toggle').on('click', function() {
        $('#sub-feature').toggleClass('expanded');
    });

    // Social media slideout
    $('.sm-slide').on('click', function() {
        $('#sm-slideout').toggleClass('open');
        $('body').toggleClass('open-slideout');
    });

    // Sticky header if scrolled
    $(window).on('scroll', function () {
        var scrolled = $(window).scrollTop() > 0;
        $('body').toggleClass('fixed', scrolled);
    }).trigger('scroll');

    // Communities CTA
    $('.communities-nav ul').Splitlist();

    // Load Images
    $('.photo').Images({
        onLoad: false
    });

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

    // Toggle Navigation
    $('nav h4').on('click', function () {
        $(this).next('div').toggleClass('hidden-medium hidden-small');
    });

    // Load images & adjust height
    $('.listings').Images({
        resize: {
            method: 'crop',
            bgColor: '#fff'
        },
        onComplete : function (el) {
            $(el).closest('.listing').find('.flag').removeClass('hidden');
            $.fn.eqHeight && $('.listings').eqHeight();
        }
    });

    // Fix equal height on resize
    var $listings = $('.listings');
    if ($listings.length > 0 && $.fn.eqHeight) {
        $(window).bind('smartresize', function () {
            $listings.eqHeight();
        });
    }

    // Save listing to Favorites
    $('.action-save[data-save]').on('click', function () {
        var $this = $(this)
            , $icon = $this.find('i')
            , $text = $this.find('span')
            , data = $this.data('save')
  ;
        $(data.div).Favorite({
            mls: data.mls,
            feed: data.feed,
            onComplete: function (response) {
                if (response.added) {
                    $icon.attr('class', 'icon-star');
                    $this.addClass('saved');
                    $text.text(data.remove);
                }
                if (response.removed) {
                    $icon.attr('class', 'icon-star-empty');
                    $this.removeClass('saved');
                    $text.text(data.add);
                }
            }
        });
        return false;
    });

})();