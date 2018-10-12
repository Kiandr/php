<?php

// Slideshow Interval (in Miliseconds, Default: 5 Seconds)
$interval = !empty($this->config['interval']) && is_numeric($this->config['interval']) ? $this->config['interval'] : 4000;

?>
// <script>
(function () {
    'use strict';
    var $module = $('#<?=$this->getUID(); ?>')
        , $slides = $module.find('.slide')
        , delay = <?=intval($interval); ?>
    ;
    if ($slides.length > 1) {

        // Hide first slide and start the show
        $slides.not('.active').css('opacity', 0);
        setInterval(function () {
            var $slide = $slides.filter('.active')
                , $next = $slide.next()
            ;

            // Don't change slide if form is in focu
            if ($slide.find(':input:focus').length > 0) return;

            // If we are going to the next in dom then we can use this animation
            if ($next.length) {
                $next.animate({ opacity: 1 }, 600, function () {
                    $slide.css('opacity', 0).removeClass('active').addClass('hidden');
                }).addClass('active').removeClass('hidden');

            // Next slide is first in dom, we need to fadeout and then hide after animation.
            } else {
                $next = $slides.first();
                $next.css('opacity', 1).addClass('active').removeClass('hidden');
                $slide.animate({ opacity: 0 }, 600, function() {
                    $slide.removeClass('active').addClass('hidden');
                });
            }

        }, delay);

        // Auto complete for search form
        var $feed = $module.find('input[name="feed"]');
        $module.find('input.autocomplete').Autocomplete({
            multiple: true,
            params: function () {
                return { feed: $feed.val() };
            }
        });

    }
    var $feed = $('input[name="feed"]');
    var $feeds = null;
    if ($feed.length) {
        $feeds = $('ul.idx-feed-select li a').each( function () {
           $(this).on('click', function () {
               var $this = $(this);
               $feed.val( $this.data('feed'));
               $feeds.removeClass("selected");
               $this.addClass("selected");
           });
        });
    }

})();