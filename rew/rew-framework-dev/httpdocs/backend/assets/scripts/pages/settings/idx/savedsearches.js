// Toggle saved searches editors
const $legacy_message = $('#legacy_message');
const $responsive_message = $('#responsive_message');
const $saved_searches_subject = $('input[name="params[message][subject]"]');

$('input[name="savedsearches_responsive"]').on('change', function () {
    const $this = $(this), value = $this.val();
    if (value === 'false') {
        $legacy_message.removeClass('hidden');
        $responsive_message.addClass('hidden');
        $saved_searches_subject.prop('required', true);
    } else {
        $legacy_message.addClass('hidden');
        $responsive_message.removeClass('hidden');
        $saved_searches_subject.removeProp('required');
    }
});

// Toggle custom sender fieldset
$('input[name="params[sender][from]"]').on('change', function () {
    const custom = this.value == 'custom' && this.checked;
    $('#responsive-from').toggleClass('hidden', !custom);
});
