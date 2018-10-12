import defaultOptions from './defaultOptions';

// Default style formats
const defaultStyleFormats = [
    { title: 'Heading 1', block: 'h1' },
    { title: 'Heading 2', block: 'h2' },
    { title: 'Heading 3', block: 'h3' },
    { title: 'Heading 4', block: 'h4' },
    { title: 'Bold text', inline: 'strong' },
    { title: 'Emphasis', inline: 'em' },
    { title: 'Strike', inline: 'strike' },
    { title: 'Blockquote', block: 'blockquote', wrapper: true }
];

// Style formats for content
// (used on non-email editors)
const contentStyleFormats = [
    { title: 'Mute', inline: 'span', classes: 'mute' },
    { title: 'Image Right', selector: 'p', classes: 'pright' },
    { title: 'Image Left', selector: 'p', classes: 'pleft' },
    { title: 'Full Video', selector: 'p,div', classes: 'video' },
    { title: 'Video Right', selector: 'p,div', classes: 'video-pright' },
    { title: 'Video Left', selector: 'p,div', classes: 'video-pleft' }
];

// Fix {tag} usage when converting URLs
const urlConverterCallback = function (url, node, on_save) {
    if (url.match(/^{/) && url.match(/}$/)) return url;
    var t = this, s = t.settings, cb = s.urlconverter_callback;
    s.urlconverter_callback = false;
    url = t.convertURL(url, node, on_save, true);
    s.urlconverter_callback = cb;
    return url;
};

// Default editor toolbar
const defaultToolbar = 'styleselect alignment | bullist numlist | link anchor | image media | code';

// "Simple" editor toolbar
const simpleToolbar = 'emoticons bold italic strikethrough | bullist numlist | link unlink | image media | code';

// "Super Simple" editor toolbar
const superSimpleToolbar = 'emoticons bold italic strikethrough | bullist numlist | link unlink';

export default (target, options = {}) => {
    const $this = $(target);

    // Check if should use "simple" toolbar
    const isSimple = $this.hasClass('simple');

    // Check if shoudd use "simple" toolbar
    const isSuperSimple = $this.hasClass('super') && isSimple;

    // Check if should use "email" editor
    const isEmail = $this.hasClass('email');

    // Choose the toolbar to use for the editor
    const toolbar = isSuperSimple ? superSimpleToolbar : isSimple ? simpleToolbar : defaultToolbar;

    const init_instance_callback = (editor) => {
        //eslint-disable-next-line no-console, no-undef
        if (__DEV__) console.log(`tinyMCE editor "${editor.id}" initialized`);

        // Call onInit
        if (typeof options.init_instance_callback === 'function') {
            options.init_instance_callback(editor);
        }

    };

    // Return all combined options
    return $.extend({}, defaultOptions, options, {
        readonly: $this.prop('readonly'),
        toolbar1: toolbar,
        urlconverter_callback: isEmail ? urlConverterCallback : false,
        style_formats: isEmail ? defaultStyleFormats : defaultStyleFormats.concat(contentStyleFormats),
        remove_script_host: isEmail ? false : defaultOptions.remove_script_host,
        content_css: isEmail ? false : defaultOptions.content_css,
        init_instance_callback,
        target
    });

};
