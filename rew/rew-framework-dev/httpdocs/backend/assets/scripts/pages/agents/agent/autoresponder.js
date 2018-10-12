// Toggle tinymce editor instance
const $document = $('#document');
const $toggle = $('input[name="ar_is_html"]');
$toggle.on('change', function () {
    const editor = $document.tinymce();
    if (this.checked && this.value === 'false') {
        if (editor) editor.remove();
        $document.addClass('off');
    } else if (!editor) {
        $document.removeClass('off').setupTinyMCE();
    }
});

// Force HTML message if using template
$('select[name="ar_tempid"]').on('change', function () {
    const editor = $document.tinymce();
    if (this.value && this.value.length > 0) {
        if (!editor) $document.removeClass('off').setupTinyMCE();
        $toggle.prop('disabled', true).prop('checked', true);
        $toggle.filter('[value="true"]').prop('checked', true);
    } else {
        $toggle.prop('disabled', false);
    }
});