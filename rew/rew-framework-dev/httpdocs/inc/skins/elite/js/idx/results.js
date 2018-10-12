// Functions for IDX Search Results

var Cookie = require('../utils/cookie');

(function() {
    var allowedViews = ['grid', 'detailed'];

    REW.activateTarget = function(target, $parent, isUiEvent) {
        $parent = $parent || $(document);
        var targetView = target.replace('view-', '');

        $parent.find('[data-' + target + ']').each(function () {
            var $this = $(this);
            $this.attr('class', $this.data(target));
            $('.idx-search [name="view"]').val(targetView);

            // Update location fragment so that if a user copies their url it will include their
            // current view
            window.location.hash = '#' + targetView;
        });

        $(window).trigger('resize'); // REWMOD Max.K 2016-10-13 - this will rerun matchHeight - fix for https://realestatewebmasters.atlassian.net/browse/GOJ-278 #1

        if (isUiEvent) {
            // Don't bother setting a cookie if the user isn't changing it.
            Cookie('results-view', targetView, {path: '/'});
        }
    };

    var matched = false;
    if (window.location.hash) {
        var fragmentView = window.location.hash.substring(1);
        if ($.inArray(fragmentView, allowedViews) > -1) {
            REW.activateTarget('view-' + fragmentView, undefined, true);
        }
    }

    var $links = $('#view-' + allowedViews.join(', #view-'));
    $links.on('click', function () {
        REW.activateTarget($(this).attr('id'), undefined, true);
    });

    if (!matched) {
        $links.each(function () {
            var $this = $(this);
            if ($this.hasClass('selected')) {
                REW.activateTarget($this.attr('id'));
                matched = true;
            }
        });
    }

    if (!matched) {
        var useView = allowedViews[0];
        if (fragmentView) {
            useView = fragmentView;
        }
        REW.activateTarget('view-' + useView);
    }
})();
