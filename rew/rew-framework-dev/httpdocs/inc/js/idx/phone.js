(function () {
    'use strict';

    // Support for textarea[maxlength]
    var $message = $('textarea[name="message"]')
        , $remaining = $message.parent().find('.remaining')
        , maxLength = parseInt($message.data('maxlength'))
        , checkLength = function () {
            var value = $message.val()
                , length = value.length
                , remaining = maxLength - length
   ;
            if (remaining < 0) $message.val(value.substr(0, maxLength));
            $remaining.html(remaining < 0 ? 0 : remaining);
        }
 ;
    $message.on('keydown', function () {
        setTimeout(checkLength, 50);
    }).trigger('keydown');

})();