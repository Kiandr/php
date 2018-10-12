(function() {
    'use strict';

    /**
     * Slideout off-screen nav menu
     * <div id="nav" class="inactive"></div>
     * <a id="nav-toggle"></a>
     * <a id="nav-close"></a>
     */
    var transitionEvent = (function () {
        var t, el = document.createElement('fakeelement');
        var transitions = {
            'transition'      : 'transitionend',
            'OTransition'     : 'oTransitionEnd',
            'MozTransition'   : 'transitionend',
            'WebkitTransition': 'webkitTransitionEnd'
        };
        for (t in transitions){
            if (el.style[t] !== undefined){
                return transitions[t];
            }
        }
    })();

    // Prevent bubbling of off-screen nav clicks
    var $nav = $('#nav').on('click', function (e) {
            e.stopPropagation();
        }), showNav = function () {
            $nav.removeClass('inactive');
            $nav.attr('aria-hidden', 'false');
            $('body').addClass('nav-open');
            $nav.addClass('animating').removeClass('nav-done').one(transitionEvent, function () {
                $nav.addClass('nav-done').removeClass('animating');
            });
        }, hideNav = function () {
            $nav.addClass('inactive');
            $nav.attr('aria-hidden', 'true');
            $('body').removeClass('nav-open');
            $nav.addClass('animating').removeClass('nav-done').one(transitionEvent, function () {
                $nav.addClass('nav-done').removeClass('animating');
            });
        };

    // Toggle display of off-screen navigation
    $('#nav-toggle').on('click', function (e) {
        e.stopPropagation();
        if ($nav.hasClass('inactive')) {
            showNav();
            $(document).on('click', function () {
                hideNav();
            });
        } else {
            hideNav();
        }
    });

    // Close off-screen navigation menu
    $('#nav-close').on('click', function () {
        hideNav();
    });

    /**
     * Toggle sidebar navigation for content pages
     * <div id="sidebar-nav"></div>
     * <a id="sidebar-toggle"></a>
     */
    var $sideNav = $('#sidebar-nav');
    $('#sidebar-toggle').on('click', function () {
        var $icon = $(this).find('i.fa');
        if ($sideNav.is('.active')) {
            $icon.removeClass('fa-caret-up').addClass('fa-caret-down');
            $sideNav.removeClass('active');
        } else {
            $icon.removeClass('fa-caret-down').addClass('fa-caret-up');
            $sideNav.addClass('active');
        }
    });

})();