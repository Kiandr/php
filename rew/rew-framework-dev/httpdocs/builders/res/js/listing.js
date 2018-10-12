require(['carousel'], function() {
    $(function() {

        // Add page specific class to body tag
        $('body').addClass('builder-listing');


        $('.flexslider').flexslider({
            animation	: 'slide',
            controlNav	: false,
            init : function() {
                $('.bdx-slideshow .slides li').Images({
                    resize : {
                        method	: 'scale'
                    }
                });
            }
        });

    });
});