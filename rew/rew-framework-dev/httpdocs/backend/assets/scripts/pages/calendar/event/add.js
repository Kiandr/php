import 'pickadate/lib/picker.date';
import 'pickadate/lib/picker.time';
import CALENDAR from 'constants/calendar';

// Load interval for calendar events
const TIME_INTERVAL = CALENDAR.time_interval;

let date_format = {
    format: 'mmmm d, yyyy',
    formatSubmit: 'yyyy-mm-dd'
};

// Date Picker
let $date_pickers = $('input[name="start_date"], input[name="end_date"]');
$date_pickers.pickadate(date_format);
// Preset Date
$date_pickers.each(function () {
    let $picker = $(this).pickadate('picker');
    let date = $(this).data('value');

    if (date != undefined && date != null) {
        $picker.set('select', date, {format: 'yyyy-mm-dd'});
    }
});

// Time Picker
let $time_pickers = $('input[name="start_time"], input[name="end_time"]');
$time_pickers.pickatime({interval: TIME_INTERVAL, formatSubmit: 'HH:i'});
// Preset Time
$time_pickers.each(function () {
    let $picker = $(this).pickatime('picker');
    let time = $(this).data('value');

    if (time != undefined && time != null) {
        $picker.set('select', time, {format: 'HH:i'});
    }
});

// All Day Toggle
let $hide_on_all_day = $('.hide-on-all-day');

let $all_day = $('input[name="all_day"]');

$all_day.on('click', function () {
    if ($(this).is(':checked')) {
        $hide_on_all_day.addClass('hidden');
    } else {
        $hide_on_all_day.removeClass('hidden');
    }
});

// Check Loaded State, And Hide If Checked
if ($all_day.is(':checked')) {
    $hide_on_all_day.addClass('hidden');
}
