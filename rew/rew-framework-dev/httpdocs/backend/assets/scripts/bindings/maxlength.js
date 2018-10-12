// Show remaining chars for textarea[maxlength] fields
$('textarea[maxlength]').each(function () {
    const $input = $(this);
    const $label = $('<label />').insertAfter($input);
    const maxlength = parseInt($input.attr('maxlength'));
    $input.on('keyup.maxlength blur.maxlength', function () {
        const value = $input.val();
        const length = value.length;
        if (length > maxlength) $input.val(value.substr(0, maxlength));
        $label.text(`You have ${maxlength - length} of ${maxlength} characters remaining`);
    }).trigger('keyup.maxlength');
});