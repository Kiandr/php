import 'jquery-tinymce';
import tinymce from 'tinymce';
import { saveFormData } from 'bootstrap/forms';
import getTinyMCEOpts from './tinymce/getTinyMCEOpts';

/**
 * Setup TinyMCE Editor for <textarea> elements
 * @example $('textarea.tinymce').setupTinyMCE();
 */
$.fn.setupTinyMCE = function () {

    // Initialize TinyMCE
    const $el = $(this).not('.off');
    $el.each(function () {
        const $this = $(this);

        // Generate unique ID selector
        let selector = $this.attr('id');
        if (!selector || selector.length < 1) {
            selector = tinymce.DOM.uniqueId();
            $this.attr('id', selector);
        }

        // Check if editor already exists
        const editor = tinymce.get(selector);
        if (editor) return true;

        // Merge final tinyMCE configuraton options for this instance
        const tinyMCEOpts = getTinyMCEOpts(this, {
            selector: `#${selector}`,
            init_instance_callback(editor) {
                saveFormData(); // Update saved form data to match tinyMCE values

                // Add data-no-link to prevent link to be clickable on preview mode
                if ($this.prop('readonly') && typeof $this.data('no-link') !== 'undefined') {
                    $(editor.getBody()).find('a').on('click', function (e) {
                        e.preventDefault();
                    });
                }
            }
        });

        // Initialize TinyMCE
        tinymce.init(tinyMCEOpts);

    });

    // Update hidden TinyMCE values on submit
    $el.closest('form').on('submit.tinymce', () => {
        const id = $(this).attr('id');
        const editor = tinymce.get(id);
        if (editor && editor.isHidden()) {
            var value = this.value;
            editor.setContent(value);
        }
    });

};

// Fix for REWCRMP2-239: Cannot add url to action plan tasks
if ($.ui && $.ui.dialog && $.ui.dialog.prototype._allowInteraction) {
    $.ui.dialog.prototype._allowInteraction = function(e) {
        return $(e.target).closest('.ui-dialog, .ui-datepicker, .select2-drop, .mce-window').length > 0;
    };
}