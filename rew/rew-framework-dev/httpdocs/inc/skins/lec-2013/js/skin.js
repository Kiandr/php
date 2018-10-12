/*! Skin JS */
(function() {

    // Communities CTA
    $('.communities-nav ul').Splitlist();
	
    // Load & Re-Size Photos
    $('.photo').Images({
        resize : { 
            method : 'crop'
        },
        onComplete : function (el) {
            $(el).closest('.listing').find('.flag').removeClass('hidden');
        }
    });
	
    // Toggle Navigation (Mobile View)
    $('#head nav').each(function() {
        var $this = $(this), $links = $(this).find('ul');
        $this.find('h4').bind(BREW.events.click, function() {
            if ($links.hasClass('hidden-phone')) {
                $links.removeClass('hidden-phone');
            } else {
                $links.addClass('hidden-phone');
            }
        });
    });
	
    // Popup Links
    $('a.popup').on(BREW.events.click, function () {
        var $this = $(this), href = $this.attr('href'), options = $this.data('popup');
        $.Window($.extend(options, {
            'iframe' : href
        }));
        return false;
    });

    // Detect Mouse Support
    $(window).one({
        mouseover : function() {
            $('html').removeClass('touch').addClass('mouse');
        }
    });

    // Save listing to Favorites
    $('#content').on('click', 'a.action-favorite[data-save]', function () {
        var $link = $(this)
            , data = $link.data('save')
  ;
        $(data.div).Favorite({
            mls: data.mls,
            feed: data.feed,
            onComplete: function (response) {
                if (response.added) {
                    $link.text(data.remove);
                }
                if (response.removed) {
                    $link.text(data.add);
                }
            }
        });
        return false;
    });
	
    // RealtyTrac - Sidebar Toggle
    if ($('body').is('.rt.nearby_sold') && $('#sidebar').length > 0) {
        var title = 'Search Form';
		
        // Toggle Button
        var $toggle = $('<a id="toggle-sidebar" class="btn">' + title + '</a>').prependTo('#content');
        $toggle.on(BREW.mobile ? 'touchstart' : 'click', function() {
            $('#sidebar').toggleClass('open');
            return false;
        });
		
        // Close Sidebar
        var $close = $('<div class="btnset close"><a class="btn"><i class="icon-remove"></i></a></div>').appendTo('#sidebar');
        $close.on(BREW.mobile ? 'touchstart' : 'click', 'a', function() {
            $('#sidebar').removeClass('open');
        });
    }

})();