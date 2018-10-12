import './errors';
import 'plugins/setupTinyMCE';
import 'plugins/rew_uploader';
import './timeline';
import './notices';
import './forms';
import './popup';
import './search';

// Setup tinyMCE editors
$('textarea.tinymce').setupTinyMCE();

// Setup uploader form fields
$('div[data-uploader]').each(function () {
    const $this = $(this);
    const opts = $this.data('uploader');
    $this.rew_uploader(opts || {});
});

// Remove body.preload classname
$(window).on('load', function() {
    $('body').removeClass('preload');
});
