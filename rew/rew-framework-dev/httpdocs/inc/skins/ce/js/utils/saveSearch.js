/**
 * Save IDX Search
 * @param {Object} search
 */
var saveSearch = function (search) {

    // Search Data
    var search = $.extend(search, {
        search_title: search.search_title ? search.search_title : (search.save_prompt ? search.save_prompt : '')
    });

    // Window HTML
    var content = '<form>' +
        '<div class="notice"><div class="notice__message">Save this search to receive updates of new listings.</div></div>' +
        '<div class="field">' +
            '<label class="field__label">Search Title</label>' +
            '<input class="-width-1/1" name="search_title" value="' + (search.search_title.length > 0 ? search.search_title : 'My Saved Search') + '" required>' +
        '</div>' +
        '<div class="field">' +
            '<label class="field__label">Email Frequency</label>' +
            '<select class="-width-1/1" name="frequency">' +
                '<option value="never"' + (search.frequency == 'never' ? ' selected' : '') + '>Never</option>' +
                '<option value="immediately"' + (search.frequency == 'immediately' ? ' selected' : '') + '>Immediately</option>' +
                '<option value="daily"' + (search.frequency == 'daily' ? ' selected' : '') + '>Daily</option>' +
                '<option value="weekly"' + (!search.frequency || search.frequency == 'weekly' ? ' selected' : '') + '>Weekly</option>' +
                '<option value="monthly"' + (search.frequency == 'monthly' ? ' selected' : '') + '>Monthly</option>' +
            '</select>' +
        '</div>' +
        '<div class="field">' +
            '<input type="checkbox" name="email_results_immediately" value="true"> Email Results Immediately' +
        '</div>' +
        '<div class="buttons -pad-top">' +
            '<button type="submit" class="button button--strong button--pill">Save Search</button>' +
        '</div>' +
    '</form>';

    // Open Window
    $.Window({
        open: true,
        width: 400,
        title: 'Save this Search',
        content: content,
        onOpen: function (win) {
            var criteria = search, $el = win.getWindow(), $msg = $el.find('.notice'), $form = $el.find('form').on('submit', function () {
                $msg.removeClass('notice--positive notice--negative').addClass('notice--note').html('Processing Request...');
                var email_results_immediately = $form.find('input[name="email_results_immediately"]').is(':checked') ? 'true' : 'false';
                var search = $.extend(criteria, {
                    search_title: $form.find('input[name="search_title"]').val(),
                    frequency: $form.find('select[name="frequency"]').val(),
                    email_results_immediately: email_results_immediately,
                    save_search: true
                });
                $.ajax({
                    url: '/idx/inc/php/ajax/json.php?saveSearch',
                    type: 'POST',
                    data: search,
                    dataType: 'json',
                    success: function (json, textStatus) {
                        if (typeof(json) == 'undefined' || !json) return;
                        if (json.success) {
                            $msg.html(json.success + '<br />').addClass('notice--positive').removeClass('notice--note');
                            if (search.create_search && search.lead_id) {
                                window.location = '/backend/leads/lead/searches/?id=' + search.lead_id;
                            } else if (json.search) {
                                if (search.search_by === 'map') {
                                    window.location = '/idx/map/?saved_search_id=' + json.search;
                                } else {
                                    window.location = '/idx/search/' + json.search + '/';
                                }
                            }
                        } else {
                            if (json.error) {
                                $msg.html(json.error).addClass('notice--negative').removeClass('notice--note');
                            }
                            if (json.redirect) {
                                $.Window({
                                    iframe: json.redirect
                                });
                            }
                        }
                    }
                });
                return false;
            });
            if (search.trigger) $form.trigger('submit');
        }
    });

};