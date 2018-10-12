// Check all
const $all = $('#check_all').on('change', function () {
    $publish.prop('disabled', !this.checked);
    $delete.prop('disabled', !this.checked);
    $check.prop('checked', this.checked);
}).prop('checked', false);

// Check one
const $check = $('input[name="comments[]"]').on('change', function () {
    const count = $check.filter(':checked').length;
    $all.prop('checked', count == $check.length);
    $publish.prop('disabled', count < 1);
    $delete.prop('disabled', count < 1);
}).prop('checked', false);

// Confirm delete
const $delete = $('#btn-delete').on('click', function () {
    const $form = $(this).closest('form');
    if (confirm ('Are you sure you want to delete the selected blog comments?')) {
        $form.find('input[name="action"]').val('delete');
        $form.trigger('submit');
    }
    return false;
});

// Confirm publish
const $publish = $('#btn-publish').on('click', function () {
    const $form = $(this).closest('form');
    if (confirm ('Are you sure you want to publish the selected blog comments?')) {
        $form.find('input[name="action"]').val('publish');
        $form.trigger('submit');
    }
    return false;
});
