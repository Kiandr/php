(function () {
    'user strict';

    // Directiry listing id
    var listingId = $('#directory-edit').data('id');

    // URL to uploader handler
    var uploaderScript = '/directory/inc/php/ajax/upload.php';

    // File Uploader
    $('#uploader').rew_uploader({
        extraParams: {
            type: 'directory',
            row: listingId
        },
        url_upload: uploaderScript + '?upload',
        url_delete: uploaderScript + '?delete',
        url_sort: uploaderScript + '?sort'
    });

    // Logo Uploader
    $('#logo-uploader').rew_uploader({
        multiple: false,
        extraParams: {
            logo: 'true',
            type: 'directory_logo',
            row: listingId
        },
        url_upload: uploaderScript + '?upload',
        url_delete: uploaderScript + '?delete',
        url_sort: uploaderScript + '?sort'
    });

    // Toggle Previews
    var $toggle = $('#view-as').on('click', 'a', function () {
        var $this = $(this), panel = $this.data('panel'), $panel = $(panel), text = $this.text();
        if ($panel.length > 0) {
            if ($panel.hasClass('hidden')) {
                $this.text(text.replace('Show', 'Hide'));
                $panel.hide().removeClass('hidden').slideDown();
                $toggle.find('a[data-panel]').not($this).each(function () {
                    var $this = $(this), panel = $this.data('panel'), $panel = $(panel), text = $this.text();
                    $this.text(text.replace('Hide', 'Show'));
                    $panel.slideUp(function () {
                        $panel.addClass('hidden');
                    });
                });
            } else {
                $this.text(text.replace('Hide', 'Show'));
                $panel.slideUp(function () {
                    $panel.addClass('hidden');
                });
            }
        }
    });

})();