import 'jquery-ui-timepicker-addon';

// Toggle date published based on is published boolean
$('input[name="published"]').on('change', function () {
    const published = this.checked && this.value === 'true';
    $('#publish-date').toggleClass('hidden', !published);
});

// Date picker
$('input[name="timestamp_published"]').datetimepicker({
    showButtonPanel: true,
    changeMonth: true,
    changeYear: true,
    dateFormat: 'DD, MM d, yy',
    ampm: true,
    separator: ' ',
    timeFormat: 'h:mmtt'
});
