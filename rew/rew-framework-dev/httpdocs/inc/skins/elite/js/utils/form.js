$(document).on('submit', 'form[data-confirm]', function (event) {
    var $this = $(this);
    event.preventDefault();

    UIkit.modal.confirm($this.data('confirm'), function () {
        // will be executed on confirm.
        REW.GoToURL($this.attr('action'), $this.attr('method') || 'GET', $this.serialize());
    });

    return false;
});

$(document).on('click', '[data-submit-form]', function () {
    var $form = $($(this).data('submit-form'));
    $form.trigger('submit');
});
