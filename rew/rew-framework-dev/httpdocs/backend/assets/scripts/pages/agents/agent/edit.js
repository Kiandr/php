// Add form
import '../add';

// Toggle update password options
const $passwordField = $('#update_password');
const $passwordInput = $('input[name="update_password"]');
const $passwordToggle = $('a.toggle_password').on('click', function () {
    if ($passwordInput.val() == 0) {
        $passwordField.removeClass('hidden').find('input').prop('required', true);
        $passwordToggle.text('Cancel Password Change');
        $passwordInput.val(1);
    } else {
        $passwordField.addClass('hidden').find('input').prop('required', false);
        $passwordToggle.text('Change Password');
        $passwordInput.val(0);
    }
});

$('input[name="agent_photo"]').change(function () {
    var ext = this.value.match(/.*\.(.+)$/)[1];
    var filename = this.value.match(/\\([^\\]+)$/)[1];
    switch (ext) {
    case 'jpg':
    case 'jpeg':
    case 'png':
    case 'gif':
        $('.file-manager-error').remove();
        break;
    default:
        $('.file-manager-error').remove();
        var message = '<p class="file-manager-error">' +
            filename +
            ' has invalid extension. Only JPG, JPEG, PNG, or GIF are allowed.</p>';
        $('input[name="agent_photo"]').parent().append(message);
        this.value = '';
    }
});
