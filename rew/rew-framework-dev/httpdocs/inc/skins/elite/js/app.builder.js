'use strict';

function init_builder() {

// customizations for builder here

}

if (global.REW) {
    init_builder();
} else {
    $(function () {
        init_builder();
    });
}
