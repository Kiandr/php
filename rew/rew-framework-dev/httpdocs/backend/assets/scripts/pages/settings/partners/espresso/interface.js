// Submit espresso dialer form
const $form = $('#login-espresso-form');
if ($form.length === 1) $form.trigger('submit');

// Handle dial form submission
const $dialForm = $('#dialForm');
const contacts = $dialForm.data('contacts');
$dialForm.on('submit', function () {
    const $this = $(this);
    const tpid = $this.find('select[name="tpid"]').val();
    const password = $this.find('input[name="f_password"]').val();
    const username = $this.find('input[name="f_username"]').val();
    if (tpid.length < 1 || password.length < 1 || username.length < 1) {
        return false;
    }
});

// Update hidden form values on tpid change
$('select[name="tpid"]').on('change', function () {
    const $this = $(this);
    const tpid = $this.val();
    const pass = $(this).children(':selected').attr('id');

    // Set username & password
    $('input[name="f_username"]').val(tpid);
    $('input[name="f_password"]').val(pass);

    // Inject contact ids
    let contactIds = '';
    if (contacts && contacts.length) {
        contacts.forEach(id => {
            contactIds += `<input type="hidden" name="contactid[]" value="${id}-${tpid}">`;
        });
    }
    $('#contact-ids').html(contactIds);

});
