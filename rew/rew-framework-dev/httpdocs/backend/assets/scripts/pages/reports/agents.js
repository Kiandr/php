// Quick Filters
var $filters = $('#select-filter').on('change', function () {
    var $this = $(this);
    var val = $this.val();
    if (val === 'all') {
        $dates.prop('required', false).closest('.field').addClass('hidden');
        $dates.val('all');
    } else {
        $dates.prop('required', true).prop('disabled', false).closest('.field').removeClass('hidden');
        if (val === 'custom') {
            $dates.val('');
        } else {
            var range = val.split('|');
            $('#date_start').datepicker('setDate', range[0]);
            $('#date_end').datepicker('setDate', range[1]);
        }
    }
});

// Date Pickers
var $dates = $('#date_start, #date_end').datepicker({
    minDate: new Date(2005, 0, 1),
    dateFormat: 'yy-mm-dd',
    showButtonPanel: true,
    changeMonth: true,
    changeYear: true,
    onSelect: function (selectedDate) {
        const instance = $(this).data('datepicker');
        const option = this.id == 'date_start' ? 'minDate' : 'maxDate';
        const date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
        $dates.not(this).datepicker('option', option, date);
        $filters.val('custom');
    }
});

// Submit Form
var $btn = $('#btn-update');
var $form = $btn.closest('form').on('submit', function () {
    var data = $(this).addClass('loading').serialize();
    var $report = $('#agent-report').html('');
    $btn.text('Loading...').prop('disabled', true);
    window.location.hash = data;
    $.ajax({
        url: '?ajax',
        data: data,
        success: function (report) {
            $btn.text('Generate').prop('disabled', false);
            $form.removeClass('loading');
            $report.html(report);
        }
    });
    return false;
});

// Parse the Hash & Detect Changes (Doesn't work in IE8)
var currHash = window.location.hash.replace('#', '');
$(window).on('hashchange', function() {
    var hash = window.location.hash.replace('#', '');
    if (hash.length > 0) {
        // Hash Data
        var arr = hash.split('&')
            , l = arr.length
            , values = []
            , data = {}
            , i = 0
            ;
        for (i; i < l; i++) {
            values = arr[i].split('=');
            data[values[0]] = values[1];
        }
        // Start & End Date
        if (data.start && data.end) {
            var opt = 'option[value="' + data.start + '|' + data.end + '"]';
            if ($filters.find(opt).length > 0) {
                $filters.val(data.start + '|' + data.end).trigger('change');
            } else if (data.start == 'all' && data.end == 'all') {
                $('input[name="start"]').val('all');
                $('input[name="end"]').val('all');
                $filters.val('all');
            } else {
                $('input[name="start"]').val(data.start);
                $('input[name="end"]').val(data.end);
                $filters.val('custom');
            }
        } else {
            $('input[name="start"]').val('all');
            $('input[name="end"]').val('all');
        }
        // Submit Form
        $form.not('.loading').trigger('submit');
    } else {
        $('#agent-report').html('');
    }
}).trigger('hashchange');

// Submit Form on First Load
if (currHash.length === 0) $form.trigger('submit');