<?php

// Slideshow Interval (in Miliseconds, Default: 5 Seconds)
$interval = !empty($this->config['interval']) && is_numeric($this->config['interval']) ? $this->config['interval'] : 8000;

?>
// <script>
(function () {
    'use strict';
    var $slides = $('#<?=$this->getUID(); ?> .slide')
        , delay = <?=intval($interval); ?>
        , slideInterval = null
    ;
    if ($slides.length > 1) {
        $slides.not('.active').css('opacity', 0);
        var fnSlide = function (fw) {

            //Get actual element
            var $actual = $slides.filter('.actual').eq(0);
            $slides.each(function(i, elem){
                $(elem).removeClass('next').removeClass('prev');
            });

            //If no actual element, use first active
            if (!$actual.size())
                $actual = $slides.filter('.active').eq(0);

            //Get next image depending on direction
            var $next = fw ? $actual.next().eq(0) : $actual.prev().eq(0);

            //Check end of list
            if (!$next.length) {
                $next = (fw ? $slides.first().eq(0) : $slides.last().eq(0));
            }

            //Fade in actual selected image
            $next.stop(true).animate({'opacity': 1}, 0, function () {
                var $this = $(this);
                if ($this.hasClass('360')) {
                    $this.find(".pnlm-grab").show();
                    clearInterval(slideInterval);
                }

            }).addClass('active').addClass('actual');

            //Fade out all other images
            $slides.not($next).each(function (i, v) {
                if ($(v).css('opacity') > 0) {
                    $(v).stop(true).animate({'opacity': 0}, 0, function () {
                        $slides.not($next).removeClass('active');
                        $(v).find(".pnlm-grab").hide();
                    }).removeClass('actual');
                }
            });


            // Set next and prev for lazy preloading
            $actual = $slides.filter('.active').eq(0);
            ($actual.next().length !== 0) ? $actual.next().addClass('next') : $slides.first().addClass('next');
            ($actual.prev().length !== 0   ) ? $actual.prev().addClass('prev') : $slides.last().addClass('prev');

            $('.slide.prev, .slide.active, .slide.next').each(function() {
                var image = $(this).data('background');
                $(this).css('background-image', 'url(\'' + image + '\')');
                $(this).css('filter', 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'' + image + '\', sizingMethod="scale")');
            });
        };

        //Add click function for slider controls
        $('.slider-controls .button').on('click', function (){
            //Stop Slide Interval
            clearInterval(slideInterval);

            //Resume Slide Interval
            slideInterval = setInterval(fnSlide, delay, true);

            //Run slider on direction clicked
            fnSlide(!$(this).hasClass('-left'));
        });

        //Start Slide Interval
        if (!$slides.filter('[data-vr-src]').length) {
            slideInterval = setInterval(fnSlide, delay, true);
        }
    }

})();