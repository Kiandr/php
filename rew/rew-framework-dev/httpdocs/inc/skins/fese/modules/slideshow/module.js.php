<?php

// Slideshow Interval (in Miliseconds, Default: 5 Seconds)
$interval = !empty($this->config['interval']) && is_numeric($this->config['interval']) ? $this->config['interval'] : 8000;

?>
// <script>
(function () {
    'use strict';
    var $slides = $('#<?=$this->getUID(); ?> .slide')
        , delay = <?=intval($interval); ?>
    ;
    if ($slides.length > 1) {
        $slides.not('.active').css('opacity', 0);
        setInterval(function () {
            var $slide = $slides.filter('.active')
                , $next = $slide.next()
            ;

            // If we are going to the next in dom then we can use this animation
            if ($next.length) {
                $next.animate({ 'opacity' : 1 }, 600, function () {
                    $slide.css('opacity', 0).removeClass('active');
                }).addClass('active');

            // Next slide is first in dom, we need to fadeout and then hide after animation.
            } else {
                $next = $slides.first();
                $next.css('opacity', 1).addClass('active');
                $slide.animate({ 'opacity' : 0 }, 600, function() {
                    $slide.removeClass('active');
                });
            }

        }, delay);
    }
})();