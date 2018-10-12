// REW IDX
var IDX = {};

// REW IDX
(function($) {

    /************************ IDX.Favorite ************************/

    // Favorite IDX Search Result
    IDX.Favorite = function (options, el) {

        // Options
        var $listing = $(el)
            , options = $.extend({
                mls: null,
                feed: null,
                force: null,
                onComplete: $.noop,
                onFailure: function (error) {
                    alert(error);
                }
            }, options)
  ;

        // Check if already saved (used to undo on error)
        var saved = !!$listing.hasClass('saved');

        // Instant user feedback
        if (options.force) {
            $listing.addClass('saved');
        } else {
            $listing.toggleClass('saved');
        }

        // AJAX Request
        $.ajax({
            'url'		: '/idx/inc/php/ajax/bookmark.php',
            'type'		: 'POST',
            'dataType'	: 'json',
            'data'		: {
                'force'			: options.force ? options.force : '',
                'feed'			: options.feed ? options.feed : '',
                'mls_number'	: options.mls ? options.mls : ''
            },
            'success'	: function (json, textStatus) {

                // Registration form
                if (json.register) {
                    $.Window({ iframe : json.register });

                    // Added to favorites
                } else if (json.added) {
                    $listing.addClass('saved');

                    // Removed from favorites
                } else if (json.removed) {
                    $listing.removeClass('saved');

                    // Error occurred
                } else if (json.error) {
                    $listing.toggleClass('saved', saved);
                    options.onFailure.apply(this, [json.error]);

                }

                // Execute onComplete callback
                if (options.onComplete) {
                    options.onComplete(json);
                }

            }
        });

    };

    // Savorite IDX Listing
    $.fn.Favorite = function (options) {
        return this.each(function () {
            (new IDX.Favorite(options, this));
        });
    };

    /************************ IDX.Dismiss ************************/

    // Dismiss IDX Search Result
    IDX.Dismiss = function (options, el) {

        // Options
        var $listing = $(el)
            , options = $.extend({
                mls: null,
                feed: null,
                force: null,
                onComplete: $.noop,
                onFailure: function (error) {
                    alert(error);
                }
            }, options)
  ;

        // Check if already dismissed (used to undo on error)
        var dismissed = !!$listing.hasClass('dismissed');

        // Instant user feedback
        if (options.force) {
            $listing.addClass('dismissed');
        } else {
            $listing.toggleClass('dismissed');
        }

        // AJAX Request
        $.ajax({
            'url'		: '/idx/inc/php/ajax/dismiss.php',
            'type'		: 'POST',
            'dataType'	: 'json',
            'data'		: {
                'force'			: options.force ? options.force : '',
                'feed'			: options.feed ? options.feed : '',
                'mls_number'	: options.mls ? options.mls : ''
            },
            'success'	: function (json, textStatus) {

                // Registration form
                if (json.register) {
                    $.Window({ iframe : json.register });

                    // Added to dismissed
                } else if (json.added) {
                    $listing.addClass('dismissed');

                    // Removed from dismissed
                } else if (json.removed) {
                    $listing.removeClass('dismissed');

                    // Error occurred
                } else if (json.error) {
                    $listing.toggleClass('dismissed', dismissed);
                    options.onFailure.apply(this, [json.error]);

                }

                // Execute onComplete callback
                if (options.onComplete) {
                    options.onComplete(json);
                }

            }
        });

    };

    // Dismiss IDX Listing
    $.fn.Dismiss = function (options) {
        return this.each(function () {
            (new IDX.Dismiss(options, this));
        });
    };

    /************************ IDX.Paginate ************************/

    // Load prev/next search result
    IDX.Paginate = function (options) {
        var opts = $.extend({
            mls: null,
            feed: null,
            done: $.noop,
            fail: $.noop
        }, options);
        return $.ajax({
            'url'		: '/idx/inc/php/ajax/json.php?paginateListing',
            'type'		: 'POST',
            'dataType'	: 'json',
            'data'		: {
                'feed'			: opts.feed,
                'mls_number'	: opts.mls
            }
        })
            .done(opts.done)
            .fail(opts.fail)
        ;
    };

})(jQuery);