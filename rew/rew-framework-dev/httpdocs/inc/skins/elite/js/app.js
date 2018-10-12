import '../scss/fonts/_webpack_fonts.scss';

'use strict';

global.jQuery = global.$ = require('jquery');

function init() {
    // Require libraries
    require('./utils/helpers');
    require('slick-carousel/slick/slick');
    require('uikit/core');
    require('uikit/comp/sticky');
    require('uikit/comp/lightbox');
    require('uikit/comp/slideshow');
    require('uikit/comp/slideset');

    // Require app components
    require('./idx');
    require('./modules');
    require('./dashboard');

    require('./skin');
    require('./fixes');
    require('./user');
    // Utils does the binding so it should be last.
    require('./utils');

    require('./loader');
}

if (global.REW) {
    init();
} else {
    $(function () {
        init();
    });
}
