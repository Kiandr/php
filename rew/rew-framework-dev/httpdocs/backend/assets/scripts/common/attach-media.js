import URLS from 'constants/urls';
import(/* webpackChunkName: "vendor/moxiemanager" */'moxiemanager').then(moxman => {

    // Configure moxiemanager base URL
    moxman.Env.baseUrl = URLS.moxiemanager;

    // Browse images
    const $attachMedia = $('#attach-media');
    $attachMedia.on('click', function () {
        moxman.browse({
            view: 'thumbs',
            title: 'Image browser',
            multiple: false,
            extensions: 'png,gif,jpg,jpeg',
            oninsert: function (args) {
                $attachMedia.addClass('hidden');
                var fileURL = args.files[0].url;
                var fileName = args.files[0].name;
                var media = fileURL.replace(fileName, encodeURIComponent(fileName));
                setAttachment(media);
            }
        });
    });

    // Remove media attachment
    $attachMedia.parent().on('click', '.attached-media', function () {
        if (confirm('Remove this attachment?')) {
            $attachMedia.removeClass('hidden');
            $message.prop('required', true);
            $(this).remove();
        }
    });

    // Text message body
    const $message = $attachMedia.closest('form')
        .find('textarea[name="message"]');

    // Set media attachment
    const setAttachment = function (media) {
        var $preview = $('<div class="attached-media" />')
            .append($('<img />', {
                src: media, alt: ''
            })).append($('<input />', {
                type: 'hidden',
                name: 'media',
                value: media
            })).append($('<em />', {
                html: '(click image to remove)'
            }))
        ;
        $preview.insertAfter($attachMedia);
        $message.prop('required', false);
    };

});
