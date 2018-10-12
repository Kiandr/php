// Sortable list
const $list = $('#export-data').sortable({
    items: '.panel',
    handle: 'dt.trigger',
    helper: 'clone',
    opacity: '0.6'
});

// Add column to export
const $columns = $('#export-columns');
const $form = $('#export-leads').on('click', 'button.add', function () {
    const column = $columns.val();
    const $input = $form.find('input[name="export[]"][value="' + column + '"]');
    const $item = $input.prop('disabled', false).closest('dl');
    $item.detach().removeClass('hidden').appendTo($list);
    $columns.find('option:selected').prop('disabled', true);
    $columns.val('');
    return false;
});

// Remove column from export
$list.on('click', 'a.delete', function () {
    const $this = $(this);
    const $input = $this.closest('dl').addClass('hidden').find(':input');
    $columns.find('option[value="' + $input.val() + '"]').prop('disabled', false);
    $input.prop('disabled', true);
    return false;
});

// Select all columns to export
$form.find('a[href="#all"]').on('click', function () {
    $columns.find('option').each(function () {
        if (this.value.length > 0) {
            $columns.val(this.value);
            $form.find('button.add').trigger('click');
        }
    });
    return false;
});

// Select none
$form.find('a[href="#none"]').on('click', function () {
    $list.find('a.delete').trigger('click');
    return false;
});