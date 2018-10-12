require('../utils/dialogs');

if (REW && REW.registration) {
    if (REW.registration.disallowClose) {
        // Remove close button
        $('.uk-modal.main-modal').find('.uk-modal-close').remove();
    }
    if (REW.registration.type == 'forcedVerification') {
        REW.Dialog('verify', window.REW.settings.ajax.urls.verify, !REW.registration.disallowClose);
    } else {
        REW.Dialog('register', false, !REW.registration.disallowClose);
    }
}
