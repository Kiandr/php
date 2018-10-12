(function () {
    'use strict';

    // Phone number field
    var $phone = $('input[name="phone"]')
        , $optin = $('input[name="opt_texts"]')
        , $label = $phone.siblings('label').find('small')
        , isRequired = $phone.data('required')
 ;

    // Phone optional
    if (!isRequired) {

        // Require phone if preferred method is phone/text
        $('input[name="contact_method"]').on('click', function () {
            var $this = $(this), value = this.value;
            $optin.prop('required', value === 'text');
            if (value === 'phone' || value === 'text') {
                $phone.prop('required', true);
                $label.addClass('hidden');
            } else {
                $phone.prop('required', false);
                $label.removeClass('hidden');
            }
        }).filter(':checked').trigger('click');

    }

})();