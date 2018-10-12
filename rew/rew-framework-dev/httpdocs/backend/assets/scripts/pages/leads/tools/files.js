import qq from 'legacy/fileuploader';
import showSuccess from 'utils/showSuccess';
import showErrors from 'utils/showErrors';
import URLS from 'constants/urls';
import Clipboard from 'clipboard';

// @see https://github.com/zenorocha/clipboard.js/wiki/Known-Issues
$.ui.dialog.prototype._focusTabbable = $.noop;

// Uploaded Files
const $fileManager = $('#file-manager');

// Upload File
const $uploader = $('#file-uploader');
if ($uploader.length > 0) {
    const exts = $uploader.data('exts');
    new qq.FileUploader({
        element: $uploader.get(0),
        action: '?upload',
        multiple: true,
        allowedExtensions: exts || [],
        showMessage: function (message) {
            showErrors([message]);
        },
        onComplete: function(id, file, json) {
            if (!json) return;
            // Upload successful
            if (json.success && json.upload) {
                const $list = $fileManager.find('ul.nodes__list');
                const $upload = $(json.upload).prependTo($list);
                $upload.find('.check').prop('checked', true);
                $fileManager.find('.none').remove();
            }
            // File limit exceeded
            if (json.max_exceed) {
                $uploader.addClass('hidden');
                $('#storage-exceed').removeClass('hidden');
            }
            if (json.usage) $('.storage-usage').html(json.usage);
        }
    });
}

// Dialog Window
const $dialog = $('<div>');
const dialogOpts = { width: 400, modal: true };

// Get currently selected uploads
const getSelectedUploads = () => {
    let uploads = [];
    $('input[name="files[]"]:checked').each(function () {
        const $upload = $(this).closest('[data-upload]');
        uploads.push($upload.data('upload'));
    });
    return uploads;
};


// Select all files
const $all = $('input[name="all"]').on('change', function () {
    const $label = $(this).closest('label').find('span');
    $label.text(this.checked ? 'Unselect All' : 'Select All');
    $('input[name="files[]"]').prop('checked', this.checked);
});

// Select single file
$(document).on('click', 'input[name="files[]"]', function () {
    const $files = $('input[name="files[]"]');
    const checked = $files.filter(':checked').length;
    $all.prop('checked', $files.length === checked);
    const $label = $('input[name="all"]').closest('label').find('span');
    $label.text($files.length === checked ? 'Unselect All' : 'Select All');
});

// Handle file manager delete action
$(document).on('click', '[data-action="attach"]', function () {
    const uploads = getSelectedUploads();
    const attachments = uploads.map(({ id, name }) => ({
        url: `${URLS.root}files/${id}/${name}`,
        name
    }));
    window.opener.insertEmailAttachments(attachments);
    window.close();
});

// Handle file manager delete action
$(document).on('click', '[data-action="delete"]', function () {
    const $upload = $(this).closest('[data-upload]');
    const uploads = $upload.length === 1 ? [$upload.data('upload')] : getSelectedUploads();
    if (uploads.length === 0) return;
    $dialog.html('<p>Are you sure you want to delete the following files?</p>');
    $dialog.append(`<p><ul><li>${uploads.map(u => u.name).join('</li><li>')}</li></ul></p>`);
    $dialog.dialog({ ...dialogOpts, title: 'Delete Files', buttons: {
        Confirm() {
            $(this).dialog('close');
            uploads.forEach(upload => {
                setTimeout(() => {
                    $.ajax({
                        url: '?delete',
                        type: 'POST',
                        data: {
                            upload: upload.id
                        },
                        dataType: 'json'
                    }).done(json => {
                        // Delete success
                        if (json.success) {
                            showSuccess([`${upload.name} has been deleted.`]);
                            $(`[data-id="${upload.id}"]`).fadeOut(function () {
                                $(this).remove();
                                // No uploads left, display a message
                                if ($fileManager.find('[data-upload]').length === 0) {
                                    const $list = $fileManager.find('ul.nodes__list');
                                    $list.append('<li class="nodes__branch none"><div class="nodes__wrap">There are currently no uploaded files.</div></li>');
                                }
                            });
                        }
                        // Delete error
                        if (json.error) {
                            showErrors([json.error]);
                        }
                        // Max limit exceeded
                        $uploader.removeClass('hidden', json.max_exceed);
                        $('#storage-exceed').toggleClass('hidden', !json.max_exceed);
                        // Usage exceeded
                        if (json.usage) {
                            $('.storage-usage').html(json.usage);
                        }
                    }).fail(() => {
                        showErrors(['Something went wrong.']);
                    });
                }, 100);
            });
        },
        Cancel() {
            $(this).dialog('close');
        }
    }});
});

// Handle file manager edit action
$(document).on('click', '[data-action="edit"]', function () {
    const upload = $(this).closest('[data-upload]').data('upload');
    $.ajax({
        type: 'POST',
        data: { edit: upload.id },
        dataType: 'json'
    }).done(json => {
        // Successful
        if (json.success) {
            $dialog.html(json.form);
            $dialog.dialog({
                ...dialogOpts,
                width: 'auto',
                title: upload.name,
                open: function () {
                    const $this = $(this);
                    // @see http://bugs.jqueryui.com/ticket/3768
                    $this.find(':focus').blur();
                    // Setup copy to clipboard functionality
                    var clipSuccessCount = 0;
                    var clipErrorCount = 0;
                    const clip = new Clipboard('[data-clipboard-text]');
                    clip.on('success', function () {
                        clipSuccessCount++;
                        if (clipSuccessCount <= 1) {
                            showSuccess(['Download URL copied to clipboard.']);
                        }
                    });
                    clip.on('error', function () {
                        clipErrorCount++;
                        if (clipErrorCount <= 1) {
                            showErrors(['Cannot copy URL to clipboard.']);
                        }
                    });
                },
                buttons: {
                    Save() {
                        $(this).find('form').trigger('submit');
                    },
                    Cancel() {
                        $(this).dialog('close');
                    }
                }
            });
        }
        // Error message
        if (json.error) {
            showErrors([json.error]);
        }
    }).fail(() => {
        showErrors(['Something went wrong.']);
    });
});

// Handle toggle of password field for upload edit form
$dialog.on('change', '.password[type="checkbox"]', function () {
    const $password = $dialog.find('input[name="password"]');
    $password.toggleClass('hidden', !this.checked);
    $password.prop('disabled', !this.checked);
    if (this.checked) $password.focus();
});

// Handle upload edit form submission
$dialog.on('submit', 'form[data-upload]', function (e) {
    e.preventDefault();
    const $this = $(this);
    const upload = $this.data('upload');
    $.ajax({
        url: '?save',
        type: 'POST',
        data: $this.serialize(),
        dataType: 'json'
    }).done(json => {
        if (json.success) {
            $dialog.dialog('close');
            showSuccess(['Your changes have been saved.']);
            $(json.upload).replaceAll(`[data-id="${upload}"]`);
        }
        if (json.error) {
            showErrors([json.error]);
        }
    }).fail(() => {
        showErrors(['Something went wrong.']);
    });
});