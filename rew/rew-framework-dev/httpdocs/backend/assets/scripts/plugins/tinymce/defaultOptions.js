import plugins from './defaultPlugins';
import URLS from 'constants/urls';

export default {
    skin: 'lightgray',
    plugins,
    toolbar_items_size: 'small',
    extended_valid_elements: '+div[*], +a[*], +span[*]',
    valid_children: '+a[h1|h2|h3|h4|h5|h6|p|span|div|img]',
    force_br_newlines: false,
    force_p_newlines: true,
    forced_root_block: 'p',
    menubar: false,
    relative_urls: false,
    browser_spellcheck: true,
    remove_script_host: true,
    autoresize_max_height: 500,
    content_css: window.__BACKEND__ && window.__BACKEND__.tinymce_styles,
    link_list: URLS.backend + 'inc/php/routine.cms_filelist.php',
    setup(editor) {
        editor.addButton('alignment', {
            type: 'listbox',
            icon: 'alignleft',
            text: '',
            onselect: function() {
                //eslint-disable-next-line no-undef
                tinymce.execCommand(this.value());
            },
            values: [
                { icon: 'alignleft', value: 'JustifyLeft' },
                { icon: 'alignright', value: 'JustifyRight' },
                { icon: 'aligncenter', value: 'JustifyCenter' },
                { icon: 'alignjustify', value: 'JustifyFull' }
            ],
            onPostRender: function() {
                this.value('JustifyLeft');
            }
        });
    }
};
