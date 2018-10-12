(function() {
    // Range Inputs
    $('.range').each(function () {
        var $range = $(this)
            , $min = $range.find('.min select')
            , $max = $range.find('.max select')
        ;
        if ($min.length && $max.length) {
            $min.on('change', function () {
                var min = parseInt($min.val())
                    , max = parseInt($max.val())
                ;

                if (min > max) {
                    $max[0].selectedIndex = 0;
                    // trigger so globals will update
                    $max.trigger('keypress');
                }

            });
            $max.on('change', function () {
                var min = parseInt($min.val())
                    , max = parseInt($max.val())
                ;
                if (min > max) {
                    $min[0].selectedIndex = 0;
                    // trigger so globals will update
                    $min.trigger('keypress');
                }

            });
        }
    });
})();