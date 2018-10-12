<?php

// Load default "module.js.php"
$controller = $this->locateFile('module.js.php', __FILE__);
if (!empty($controller)) {
    require $controller;
}

?>

/* <script> */
(function () {
    window.onload = function () {

        //Check if object-fit style is supported and if not resize image
        var obj = window.getComputedStyle($('.slideset .slide img')[0], null);
        if (obj['object-fit'] === undefined ) {

            var $pictures = $('.slideset').find('.slide');

            $pictures.each( function () {
                var $this = $(this);
                var $img = $this.find('picture img');
                var imgUrl = $img.data('src');
                if (imgUrl === undefined ) {
                    imgUrl = $img.attr('src');
                }
                $this.css('background-image', 'url(' + imgUrl + ')');
                $this.addClass("compat-object-fit");
                $this.find('picture').remove();
            });

        }
    }
})();