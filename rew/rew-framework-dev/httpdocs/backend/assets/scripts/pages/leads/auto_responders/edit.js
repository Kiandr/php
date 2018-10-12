// Toggle custom sender fieldset
$('input[name="from"]').on('change', function () {
    const custom = this.value == 'custom' && this.checked;
    $('#autoresponder-from').toggleClass('hidden', !custom);
});

// Show CC email field
$('#addCC').on('click', function () {
    $('#emailCC').removeClass('hidden').find('input').trigger('focus');
    $(this).remove();
    return false;
});

// Show BCC email field
$('#addBCC').on('click', function () {
    $('#emailBCC').removeClass('hidden').find('input').trigger('focus');
    $(this).remove();
    return false;
});

// Autoresponder message
const $message = $('textarea[name="document"]');

// Toggle HTML email message
const $toggle = $('input[name="is_html"]');
$toggle.on('change', function () {
    const editor = $message.tinymce();
    if (this.checked && this.value === 'false') {
        if (editor) editor.remove();
        $message.addClass('off');
    } else if (!editor) {
        $message.removeClass('off').setupTinyMCE();
    }
});

// Force HTML message if using template
$('select[name="tempid"]').on('change', function () {
    const editor = $message.tinymce();
    if (this.value && this.value.length > 0) {
        if (!editor) $message.removeClass('off').setupTinyMCE();
        $toggle.prop('disabled', true).prop('checked', true);
        $toggle.filter('[value="true"]').prop('checked', true);
        if ($('input[name=id]').text() == 'Responsive') {
            $('div#message_block').addClass('hidden');
            $('div#preview_block').removeClass('hidden');
        }
    } else {
        $toggle.prop('disabled', false);
        if ($('input[name=id]').text() == 'Responsive') {
            $('div#message_block').removeClass('hidden');
            $('div#preview_block').addClass('hidden');
        }
    }
});