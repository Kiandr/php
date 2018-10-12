(function() {
    var selector = '[name="search_type"],[name="search_type[]"]';

    $(document).on('change', selector, function () {
        var $this = $(this);

        function toggle($el, state) {
            $el.toggleClass('uk-hidden', !state).find('input,select').attr('disabled', !state);
        }

        var value;
        if ($this.is(':checkbox')) {
            if (!$this.is(':checked')) {
                // Get the value of the first checked item
                value = $(selector).filter(':checked').eq(0).val();
                if (!value) {
                    value = '';
                }
            } else {
                value = $this.val();
            }
        } else {
            value = $this.val();
        }

        if ($.inArray(value.toLowerCase(), REW.settings.idx.rentalTypes) > -1) {
            toggle($('.sale'), false);
            toggle($('.rent'), true);
        } else {
            toggle($('.rent'), false);
            toggle($('.sale'), true);
        }
    });
})();
