import 'jquery-tinymce';
import tinymce from 'tinymce'; // eslint-disable-line no-unused-vars

// Require #body# in template
$('#template-form').on('submit', function () {
    const editor = $('#template').tinymce();
    const str = editor.getContent();
    if (str.indexOf('#body#') === -1) {
        alert('You will need to mark where the body of the email is to go by adding one #body# to the text.');
        return false;
    }
});

// Show preview of template
const $preview = $('#preview-template');
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