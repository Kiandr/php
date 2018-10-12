// Confirm revert action
$('input[name="revert"]').on('change', function () {
    const $this = $(this);
    if ($this.prop('checked')) {
        if (confirm('Are you sure you want to revert this snippet? Your changes will be lost.')) {
            $this.closest('form').removeClass('rew_check').trigger('submit');
            return false;
        }
    }
});
