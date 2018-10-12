/**
 * Save IDX Search
 * @deprecated
 */
var saveSearch = function (search) {

    // Search Data
    var search = $.extend(search, {
        search_title : search.search_title ? search.search_title : (search.save_prompt ? search.save_prompt : '')
    });

    // Form HTML
    var html = [
        '<p class="msg">Save this search to receive updates of new listings.</p>',
        '<form>',
        '<div class="field x12">',
        '<label>Search Title</label>',
        '<input name="search_title" value="' + (search.search_title.length > 0 ? search.search_title : 'My Saved Search') + '" required>',
        '</div>',
        '<div class="field x12">',
        '<label>Email Frequency</label>',
        '<select name="frequency">',
        '<option value="never"' + (search.frequency == 'never' ? ' selected' : '') + '>Never</option>',
        '<option value="immediately"' + (search.frequency == 'immediately' ? ' selected' : '') + '>Immediately</option>',
        '<option value="daily"' + (search.frequency == 'daily' ? ' selected' : '') + '>Daily</option>',
        '<option value="weekly"' + (!search.frequency || search.frequency == 'weekly' ? ' selected' : '') + '>Weekly</option>',
        '<option value="monthly"' + (search.frequency == 'monthly' ? ' selected' : '') + '>Monthly</option>',
        '</select>',
        '</div>',
        '<div class="field">',
        '<input type="checkbox" name="email_results_immediately" value="true"> Email Results Immediately',
        '</div>',
        '<div class="btnset">',
        '<button type="submit" class="strong">Save Search</button>',
        '</div>',
        '</form>'
    ];

    // Load Window
    $.Window({
        open : true,
        width : 400,
        title : 'Save this Search',
        content : html.join('\n'),
        onOpen : function (win) {

            // Bind Form Submit
            var criteria = search, $el = win.getWindow(), $msg = $el.find('.msg'), $form = $el.find('form').on('submit', function () {
                $msg.removeClass('positive negative').addClass('caution').html('Processing Request...');

                // Search Data
                var email_results_immediately = $form.find('input[name="email_results_immediately"]').is(':checked') ? 'true' : 'false';
                var search = $.extend(criteria, {
                    search_title : $form.find('input[name="search_title"]').val(),
                    frequency : $form.find('select[name="frequency"]').val(),
                    email_results_immediately: email_results_immediately,
                    save_search : true
                });

                // AJAX Request
                $.ajax({
                    'url'      : '/idx/inc/php/ajax/json.php?saveSearch',
                    'type'     : 'POST',
                    'dataType' : 'json',
                    'data'     : search,
                    'success'  : function (json, textStatus) {
                        if (typeof(json) == 'undefined' || !json) return;
                        if (json.success) {
                            // Success
                            $msg.html(json.success + '<br />').addClass('positive').removeClass('caution');
                            // Re-Direct to Backend
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
                            // Error Occurred
                            if (json.error) {
                                $msg.html(json.error).addClass('negative').removeClass('caution');
                            }
                            // Registration
                            if (json.redirect) {
                                $.Window({
                                    iframe : json.redirect
                                });
                            }
                        }
                    }
                });

                // Disable Default
                return false;

            });

            // Trigger Submit
            if (search.trigger) $form.trigger('submit');

        }
    });

    // Return
    return;

};