// REW IDX
global.IDX = {};

// REW IDX
/************************ IDX.Favorite ************************/

// Favorite IDX Search Result
IDX.Favorite = function (options, el) {

    // Options
    var $icon = $(el)
        , options = $.extend({
            mls: null,
            feed: null,
            force: null,
            onComplete: $.noop,
            onFailure: function (error) {
                UIkit.modal.alert(error);
            }
        }, options)
        ;
    if (REW.listing) {
        options.mls = REW.listing.mls;
        options.feed = REW.listing.feed;
    }

    // Check if already saved (used to undo on error)
    var saved = !!$icon.hasClass('uk-icon-heart');
    var toggle = function (saved) {
        if (saved) {
            $icon.removeClass('uk-icon-heart-o').addClass('uk-icon-heart');
            $('[data-dismiss*=\'"mls":"'+options.mls+'"\'] .uk-icon-eye-slash').removeClass('uk-icon-eye-slash').addClass('uk-icon-eye'); // removing dismissed state
        } else {
            $icon.removeClass('uk-icon-heart').addClass('uk-icon-heart-o');
            if (options.listing) {
                options.listing.removeClass('dismissed');
            }
        }
    };

    // Instant user feedback
    if (options.force) {
        toggle(!saved);
    } else {
        toggle(saved);
    }

    // AJAX Request
    $.ajax({
        'url'		: REW.settings.ajax.urls.bookmark,
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
                REW.Dialog('register');

                // Added to favorites
            } else if (json.added) {
                toggle(true);

                // Removed from favorites
            } else if (json.removed) {
                toggle(false);

                // Error occurred
            } else if (json.error) {
                toggle(saved);
                options.onFailure.apply(this, [json.error]);

            }

            // Execute onComplete callback
            if (options.onComplete) {
                options.onComplete(json);
            }

        }
    });

};

/************************ IDX.Dismiss ************************/

// Dismiss IDX Search Result
IDX.Dismiss = function (options, el) {

    // Options
    var $icon = $(el)
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
    var dismissed = !!$icon.hasClass('uk-icon-eye-slash');

    var toggle = function (dismissed) {
        if (dismissed) {
            $icon.removeClass('uk-icon-eye').addClass('uk-icon-eye-slash');
            options.listing.addClass('dismissed');
            $('[data-save*=\'"mls":"'+options.mls+'"\'] .uk-icon-heart').removeClass('uk-icon-heart').addClass('uk-icon-heart-o'); // removing saved state

        } else {
            $icon.removeClass('uk-icon-eye-slash').addClass('uk-icon-eye');
            options.listing.removeClass('dismissed');
        }
    };

    // Instant user feedback
    if (options.force) {
        toggle(!dismissed);
    } else {
        toggle(dismissed);
    }

    // AJAX Request
    $.ajax({
        'url'		: REW.settings.ajax.urls.dismiss,
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
                REW.Dialog('register');

                // Added to dismissed
            } else if (json.added) {
                toggle(true);

                // Removed from dismissed
            } else if (json.removed) {
                toggle(false);

                // Error occurred
            } else if (json.error) {
                toggle(dismissed);
                options.onFailure.apply(this, [json.error]);

            }

            // Execute onComplete callback
            if (options.onComplete) {
                options.onComplete(json);
            }

        }
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
        'url'		: REW.settings.ajax.urls.json + '?paginateListing',
        'type'		: 'POST',
        'dataType'	: 'json',
        'data'		: {
            'feed'			: opts.feed,
            'mls_number'	: opts.mls
        }
    })
        .done(opts.done)
        .fail(opts.fail);
};


IDX.deleteSearch = function (search, url) {
    var data = {'saved_search_id':search};
    if (REW.settings.lead_id) {
        data = $.extend(data, {'lead_id':REW.settings.lead_id});
        url = '/backend/leads/lead/searches/?id=' + REW.settings.lead_id;
    }

    $.ajax({
        'url'       : REW.settings.ajax.urls.deleteSearch,//'/idx/inc/php/ajax/json.php?deleteSearch',
        'type'      : 'POST',
        'dataType'  : 'json',
        'data'      : data,
        'success'   : function (json) {

            if (json.register) {

                // Registration form
                REW.Dialog('register');
            } else if (json.error) {

                // Error occurred
                UIkit.modal.alert(json.error);
            } else if (json.success) {

                // Success!
                // @TODO: add these urls to footer.tpl
                UIkit.modal.alert(json.success);

                // Redirect after 1 second
                setTimeout(function () {
                    window.location = url;
                }, 2500);
            }
        }
    });
};


function saveSearch (search, url) {
    if (REW.settings.lead_id)
        search = $.extend(search, {'lead_id':REW.settings.lead_id});

    // AJAX Request
    $.ajax({
        'url'		: url,
        'type'		: 'POST',
        'dataType'	: 'json',
        'data'		: search,
        'success'	: function (json) {

            if (json.register) {

                // Registration form
                REW.Dialog('register');
            } else if (json.error) {

                // Error occurred
                UIkit.modal.alert(json.error);
            } else if (json.success) {

                // Success!
                // @TODO: add these urls to footer.tpl
                UIkit.modal.alert(json.success);

                // Redirect after 1 second
                setTimeout(function () {
                    // Re-Direct to Backend
                    if (search.create_search && search.lead_id) {
                        window.location = '/backend/leads/lead/searches/?id=' + search.lead_id;
                    } else if (json.search) {
                        if (search.search_by === 'map') {
                            window.location = '/idx/map/?saved_search_id=' + json.search + (search.lead_id ? '&lead_id='+search.lead_id : '');
                        } else {
                            window.location = '/idx/search/' + json.search + '/' + (search.lead_id ? '?lead_id='+search.lead_id : '');
                        }
                    }
                }, 2500);
            }
        }
    });
}

// Dismiss IDX Search Result
var savedSearch = null;
IDX.SaveSearch = function (search) {
    savedSearch = $.extend(search, {
        'search_title' : search.search_title ? search.search_title : (search.save_prompt ? search.save_prompt : 'My Saved Search')
    });

    if (savedSearch.saved_search_id) {
        saveSearch(savedSearch, REW.settings.ajax.urls.editSearch);
    } else if (savedSearch.hasOwnProperty('trigger')) {
        saveSearch(savedSearch, REW.settings.ajax.urls.saveSearch);
    } else {
        REW.Dialog('create_search');
        $('.uk-modal.main-modal').one('show.uk.modal', function () {
            $('[name="search_title"]').val(savedSearch.search_title);
        });
    }
};
$(document).on('click', '.create-saved-search', function (event) {
    event.preventDefault();

    var $form = $(this).parents('form');
    var form = $form.serializeArray();
    for (var i in form) {
        var element = form[i];
        savedSearch[element.name] = element.value;
    }

    saveSearch(savedSearch, REW.settings.ajax.urls.saveSearch);
});


/************************ IDX.DisplayPhotos ************************/

// Favorite IDX Search Result
IDX.DisplayPhotos = function (options, $el) {

    // Options
    var options = $.extend({
        require_registration: false
    }, options);

    if (options.require_registration) {
        REW.Dialog('register');
        REW.Cookie('display-photos-if-registered', true);
    } else {
        $('.js-idx-details-gallery-overlay').toggleClass('uk-hidden');
        UIkit.slideshow($('.js-idx-details-gallery-overlay [data-uk-slideshow]')).init();
    }
};
