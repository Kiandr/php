import 'jquery-tinymce';
import tinymce from 'tinymce'; // eslint-disable-line no-unused-vars

// Toggle HTML / Plaintext
$('#toggle-editor').on('click', function () {
    const $this = $(this);
    const text = $this.text();
    const $doc = $('#document');
    const editor = $doc.tinymce();
    const $is_html = $('input[name="is_html"]');
    // Enable TinyMCE Editor
    if (text == 'Switch to WYSIWYG Editor') {
        $is_html.val('true');
        $this.text('Switch to Plain Text');
        $doc.removeClass('off').setupTinyMCE();
        return false;
    }
    // Disable TinyMCE Editor
    if (text == 'Switch to Plain Text') {
        $is_html.val('false');
        $this.text('Switch to WYSIWYG Editor');
        if (editor) editor.remove();
        $doc.addClass('off');
        return false;
    }
});

// Show preview of document
const $preview = $('#preview-document');
if ($preview.length === 1) {
    $preview.tinymce({
        readonly: true,
        skin: 'lightgray',
        plugins: ['wordcount autoresize'],
        body_class: 'readonly',
        statusbar: false,
        toolbar: false,
        menubar: false,
        visual: false,
        relative_urls: false,
        remove_script_host: true,
        browser_spellcheck: true
    });
}