// Lead add form
import '../add';
import groupPicker from 'common/groupPicker';

groupPicker('select[name="groups[]"]');

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
