(function () {
    'use strict';

    function close(popup) {
        if (popup) {
            popup.close();
        }
    }

    // Social connect success
    $(window).on('oauth-login', function (e, popup) {
        close(popup);

        // We're logged in. Reload the window.
        window.location.reload();
    });

    // Social connect success
    $(window).on('oauth-success', function (e, popup) {
        close(popup);

        // Connect was successful but we need more data (i.e. this is a register event)
        REW.Dialog('connect', REW.settings.ajax.urls.connect, true);
    });

    // Social connect error
    $(window).on('oauth-error', function (e, popup) {
        close(popup);

        // There was an error. Open the register dialog
        REW.Dialog('connect', REW.settings.ajax.urls.register, true);
    });

})();


global.REW.OAuth = function () {
    $(document).on('click', '.oauth[href]', function (event) {
        event.preventDefault();
        window.open($(this).attr('href'), 'Social Connect', 'toolbar=0,status=0,scrollbars=1,width=600,height=450,left=' + (screen.availWidth / 2 - 225) + ',top=' + (screen.availHeight / 2 - 250)).focus();
    });
};
