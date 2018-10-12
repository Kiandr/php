(function () {
    'use strict';

    $(REW).on('autocomplete-bind', function() {
        // Require UIKit.autocomplete
        require('uikit/comp/autocomplete');

        // AC configuration
        var url = '/idx/inc/php/ajax/json.php';
        var feed = REW.settings.idx.feed;
        var limit = 10;
        // Enable autocomplete inputs
        var $autocomplete = $('input.autocomplete');
        $autocomplete.each(function () {
            var $input = $(this).attr('autocomplete', 'off');
            var $parent = $input.parent();
            var name = $input.attr('name');
            UIkit.autocomplete($parent, {
                minLength: 1,
                delay: 200,
                source: function (done) {
                    $.ajax({
                        url: url,
                        type: 'get',
                        data: {
                            q: this.value,
                            search: name,
                            limit: limit,
                            feed: feed
                        },
                        dataType: 'json',
                        success: function (data) {
                            done(data.options);
                        }
                    });
                }
            });
        });
    });
    $(REW).trigger('autocomplete-bind');
})();