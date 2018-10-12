// Toggle registration options
const $extras = $('#registration-extras');
$('select[name="registration"]').on('change', function () {
    const $this = $(this), value = $this.val();
    if (value === '') {
        $this.data('name', $this.attr('name')).attr('name', '');
        $extras.removeClass('hidden');
    } else {
        $this.attr('name', $this.data('name'));
        $extras.addClass('hidden');
    }
});
