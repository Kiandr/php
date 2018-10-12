import 'plugins/rew_manager';
import FlatPicker from 'flatpickr';
import CALENDAR from 'constants/calendar';
import {saveFormData} from 'bootstrap/forms';

//Load interval for calendar events
const TIME_INTERVAL = CALENDAR.time_interval;

const timeInput = document.getElementById('reminder_time');
const dateInput = document.getElementById('reminder_date');
const flatpickerDefaults = {
    static: true,
    altInput: true
};

const date_format = {
    ...flatpickerDefaults,
    altFormat: 'F j, Y',
    dateFormat: 'Y-m-d',
};

const time_format = {
    ...flatpickerDefaults,
    enableTime: true,
    noCalendar: true,
    altFormat: 'h:i K',
    minuteIncrement: TIME_INTERVAL,
    dateFormat: 'H:i',
};

// Reminder date picker
FlatPicker(dateInput, date_format);

// Reminder time picker
FlatPicker(timeInput, time_format);

// Manage reminder types
$('#manage-reminder-types').rew_manager({
    type: 'eventType',
    title: 'Manage Reminder Types',
    optionText: 'Type',
    options: $('select[name="type"]')
});

saveFormData();
