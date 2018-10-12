'use strict';

function init_directory() {

    // Require app components
    require('./directory');


}

if (global.REW) {
    init_directory();
} else {
    $(function () {
        init_directory();
    });
}
