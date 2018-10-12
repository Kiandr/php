// Triggers REW breakpoint event when we move between breakpoints
(function () {
    var breakpoints = require('../../config/breakpoints.json');
    var oldMin = 0;
    var oldMax = 0;
    var $window = $(window);

    function captureResize () {
        var width = $window.innerWidth();

        var min = 0;
        var max = 0;
        var minName = '';
        var maxName = '';
        for (var name in breakpoints) {
            var size = breakpoints[name];

            if (width >= size) {
                min = size;
                minName = name;
            }
            if (min && max == 0 && width < size) {
                max = size;
                maxName = name;
            }
        }

        if (!min) {
            // The only way this is possible is if we're lower than the smallest breakpoint
            maxName = Object.keys(breakpoints)[0];
            max = breakpoints[maxName];
        }

        // Have we moved into a different breakpoint?
        if (oldMin != min || oldMax != max) {
            REW.breakpoints = {
                min: min,
                max: max,
                minName: minName,
                maxName: maxName
            };

            // Yup. Lets trigger an event
            $(REW).trigger('bp-move', [minName, maxName]);
            oldMin = min;
            oldMax = max;
        }
    }
    
    $window.resize(function () {
        captureResize();
    });
    captureResize();
})();
