(function () {
    'use strict';

    var connectUrl = '/idx/connect.html'
        , registerUrl = '/idx/register.html'
 ;

    // Social connect success
    $(window).on('oauth-login', function (e, popup) {
        if (popup) {
            popup.close();
        }
        if (window != window.parent) {
            window.parent.location.reload();
        } else {
            window.location.reload();
        }
    });

    // Social connect success
    $(window).on('oauth-success', function (e, popup) {
        if (popup) {
            popup.close();
        }
        if (window != window.parent) {
            window.location.href = connectUrl + '?popup';
        } else {
            $.Window({ iframe: connectUrl });
        }
    });

    // Social connect error
    $(window).on('oauth-error', function (e, popup) {
        if (popup) {
            popup.close();
        }
        if (window != window.parent) {
            window.location.reload();
        } else {
            $.Window({ iframe: registerUrl });
        }
    });

})();