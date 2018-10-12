(function () {
    'use strict';

    // Inquiry form fields
    var $inquiry = $('select[name="inquire_type"]')
        , $message = $('textarea[name="comments"]')
 ;


    // Require DOM element
    if ($message.length > 0) {
        var isDefault = $message.val().length === 0;

        // Message has been changed
        $message.one('keypress', function () {
            isDefault = false;
        });

        // Toggle default message
        $inquiry.on('change', function () {
            if (!isDefault) return;
            var $this = $(this)
                , $option = $this.find('option:selected')
                , message = $option.data('message')
                , value = $this.val()
   ;
            $message.val(message || '');
        }).trigger('change');

    }

})();