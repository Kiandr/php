// File Uploader
//require('./gallery');
$('#uploader').rew_uploader({
    'extraParams' : {
        'type' : 'directory',
        'row' : $(this).data('row')
    },
    'url_upload' : '/directory/inc/php/ajax/upload.php?upload',
    'url_delete' : '/directory/inc/php/ajax/upload.php?delete',
    'url_sort'   : '/directory/inc/php/ajax/upload.php?sort'
});

// Logo Uploader
$('#logo-uploader').rew_uploader({
    'multiple' : false,
    'extraParams' : {
        'logo' : 'true',
        'type' : 'directory_logo',
        'row' : $(this).data('row')
    },
    'url_upload' : '/directory/inc/php/ajax/upload.php?upload',
    'url_delete' : '/directory/inc/php/ajax/upload.php?delete',
    'url_sort'   : '/directory/inc/php/ajax/upload.php?sort'
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
