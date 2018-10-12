(function () {
    'user strict';

    // URL to uploader handler
    var uploaderScript = '/directory/inc/php/ajax/upload.php';

    // File Uploader
    $('#uploader').rew_uploader({
        url_upload: uploaderScript + '?upload',
        url_delete: uploaderScript + '?delete',
        url_sort: uploaderScript + '?sort',
        extraParams: {
            type: 'directory'
        }
    });

    // Logo Uploader
    $('#logo-uploader').rew_uploader({
        url_upload: uploaderScript + '?upload',
        url_delete: uploaderScript + '?delete',
        url_sort: uploaderScript + '?sort',
        multiple: false,
        extraParams: {
            'type': 'directory_logo'
        }
    });

})();