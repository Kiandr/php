// Toggle destination type fields
$('input[name="type"]').on('change', function () {
    const selector = `[data-group-name="${this.value}"]`;
    $('[data-group-name]').hide();
    $(selector).show();
});

// Update placeholder hints on URL change
const $customURL = $('#custom_url');
$customURL.bind('keyup blur', function () {
    $customURLFieldsets.each(function () {
        const $fieldset = $(this);
        const $url = $fieldset.find('[type="text"]');
        const $hint = $fieldset.find('.hint');
        let uri = '';
        // Domain
        if ($customURL.val().length) {
            uri = $customURL.val();
        } else {
            uri = $customURL.attr('placeholder');
        }
        // URI
        if ($url.val().length) {
            uri += $url.val();
        } else {
            uri += $url.attr('placeholder');
        }
        $hint.text('POST ' + uri);
    });
});

// Bind URL change events
const $customURLFieldsets = $('.fieldset-custom-destination');
$customURLFieldsets.each(function () {
    const $url = $(this).find('[type="text"]');
    $url.bind('keyup blur', function () {
        const $hint = $url.next('.hint');
        let uri = '';
        // Domain
        if ($customURL.val().length) {
            uri = $customURL.val();
        } else {
            uri = $customURL.attr('placeholder');
        }
        // URI
        if ($url.val().length) {
            uri += $url.val();
        } else {
            uri += $url.attr('placeholder');
        }
        $hint.text('POST ' + uri);
        return true;
    });
});

// Toggle event details
$customURLFieldsets.on('change', '[type="checkbox"]', function () {
    const $details = $(this).parent('label').next('.details');
    this.checked && $details.show() || $details.hide();
});

// Trigger event
$customURL.trigger('blur');
