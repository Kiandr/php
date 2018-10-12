$(document).on('click', '[data-dashboard]', function (event) {
    event.preventDefault();

    var url = REW.settings.ajax.urls.dashboard;
    if (url.indexOf('?') == -1) {
        url += '?';
    } else {
        url += '&';
    }
    REW.Dialog('dashboard', url + 'view=' + $(this).data('dashboard'));
});
