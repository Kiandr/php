// Initializes Slick on the element(s) provided, with the config provided. This is the single point
// that shold be used to initialize Slick so that if we choose a different slideshow later on, we
// can simply adapt the config here.

global.REW.Slideshow = function ($element, config) {
    $element.slick(config);

    // check if browser window has focus
    var notIE = (document.documentMode === undefined),
        isChromium = window.chrome;

    if (!REW.Helpers.isMobile) {
        REW.Helpers.vis(function(){
            if(REW.Helpers.vis()){
                // tween resume() code goes here
                setTimeout(function(){
                    $element.slick('setPosition');
                },300);

            }
        });

        if (notIE && !isChromium) {

            // checks for Firefox and other  NON IE Chrome versions
            $(window).on('focusin', function () {

                // tween resume() code goes here
                setTimeout(function(){
                    $element.slick('setPosition');
                },300);

            }).on('focusout', function () {
                // tween pause() code goes here
                $element.slick('setPosition');

            });

        } else {

            // checks for IE and Chromium versions
            if (window.addEventListener) {

                // bind focus event
                window.addEventListener('focus', function (event) {

                    // tween resume() code goes here
                    setTimeout(function(){
                        $element.slick('setPosition');
                    },300);

                }, false);

                // bind blur event
                window.addEventListener('blur', function (event) {
                    // tween pause() code goes here
                    $element.slick('setPosition');

                }, false);

            } else {

                // bind focus event
                window.attachEvent('focus', function (event) {
                    // tween resume() code goes here
                    setTimeout(function(){
                        $element.slick('setPosition');
                    },300);

                });

                // bind focus event
                window.attachEvent('blur', function (event) {
                    // tween pause() code goes here
                    $element.slick('setPosition');

                });
            }
        }
    }
};