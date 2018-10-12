const popupAttribute = 'popup';

/**
 * Checks if the current request is within a popup (query param popup)
 * @returns {boolean}
 */
function isPopup () {
    return window.location.href.indexOf('?' + popupAttribute) >= 0
        || window.location.href.indexOf('&' + popupAttribute) >= 0;
}

/**
 * Appends a popup query parameter to href
 * @param href
 * @returns {string}
 */
function appendIndicator (href) {
    if (href.indexOf('?') === -1) {
        href += '?' + popupAttribute + '=true';
    } else {
        href += '&' + popupAttribute + '=true';
    }

    return href;
}

if (isPopup()) {
    // Append popup=true Query String
    $('a').not('.omit').each(function () {
        const $this = $(this);
        const href = $this.get(0).href;
        const target = $this.attr('target');
        if (target !== '_blank' && target !== '_parent' && href && href.length > 0 && href.indexOf('javascript:') === -1) {
            $this.attr('href', appendIndicator(href));
        }
    });

    // Append popup=true Query String
    $('form').each(function () {
        const $this = $(this);
        const action = $this.get(0).action;
        if (action && action.length > 0) {
            $this.attr('action', appendIndicator(action));
        }
    });

    // Close Popup Window
    $('.btn.close').on('click', function () {
        if (confirm('Are you sure you want to close this window?')) {
            window.close();
        }
    });
}
