/**
 * Edit IDX Search
 * @deprecated
 */
var editSearch = function (search) {

    // Search criteria to save
    var data = $.extend({}, search, {
        edit_search: null
    });

    // AJAX Request
    $.ajax({
        'url'      : '/idx/inc/php/ajax/json.php?editSearch',
        'type'     : 'POST',
        'dataType' : 'json',
        'data'     : data,
        'success'  : function (json, textStatus) {
            if (typeof(json) == 'undefined' || !json) return;
            if (json.success) {
                if (search.edit_search && search.lead_id) {
                    window.location = '/backend/leads/lead/searches/?id=' + search.lead_id;
                } else if (search.saved_search_id && search.search_by == 'map') {
                    window.location = '/idx/map/?saved_search_id=' + search.saved_search_id;
                } else if (search.saved_search_id) {
                    window.location = '/idx/search/' + search.saved_search_id + '/';
                }
            } else if (json.error) {
                alert(json.error);
            }
        }
    });

    // Return
    return;

};