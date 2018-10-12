import URLS from 'constants/urls';

// Acquire App Data
const $calendar = $('div[data-calendar]').data('calendar');

let $submenu = $('#cal_quick_pick ul');

let $calendar_days = $('.calendar__day');

let xhr = null;

$('#cal_quick_pick > li > a').on('click', function () {
    $submenu.toggleClass('hidden');
});

// Load Add Calendar Event Page On Calendar Date Click
$calendar_days.on('click', function () {

    let day = $(this).find('a').text();

    let date = $calendar.year + '-' + $calendar.month + '-' + day;

    window.location.href = '/backend/calendar/event/add/?date=' + date;

});

// Load Add Calendar Event Page On Calendar Time Click
$('#calendar_day .slot').on('click', function () {
    let time = $(this).attr('id');

    let date = $calendar.year + '-' + $calendar.month + '-' + $calendar.day;

    time = time.replace('-', ' ');

    window.location.href = encodeURI('/backend/calendar/event/add/?date=' + date + '&start_time=' + time);
});


/**
 * Returns markup for a passed event object
 * @param object event
 * @return string html
 */
let build_event_markup = function (event) {

    let bgClass = '';
    let eventComplete = '';

    if (event.className) bgClass = event.className;

    if (event.completed) eventComplete = 'text--strike';

    let data_event = '';

    if (typeof event.reminder !== 'undefined') {
        data_event = 'reminder';
    }

    let data_lead = '';

    if (typeof event.lead !== 'undefined') {
        data_lead = event.lead;
    }

    let html = '<div class="token w1/1 event -dB' + (event.editable == true ? ' editable' : '') + ' ' + bgClass + '"';
    html += 'data-event="' + data_event + '" data-lead="' + data_lead + '" data-id="' + event.id + '">';
    html += '<span class="token__thumb thumb thumb--tiny ' + bgClass + '"></span><span class="token__label text text--small ' + eventComplete + '">' + event.title + '</span>';
    html += '</div>';

    return html;
};

/**
 * Adds the collection of event objects to the calendar
 * @param array events
 * @return void
 */
let render_calendar_events = function (events) {

    let count = events.length;

    for (let eventIndex = 0; eventIndex < count; eventIndex++) {

        let event = events[eventIndex];
        let dt = event.formatted_start.split(/[^\d]+/);
        let eventStartDate = new Date(dt[0],dt[1]-1,dt[2],dt[3],dt[4],dt[5]);
        dt = event.formatted_end.split(/[^\d]+/);
        let eventEndDate = new Date(dt[0],dt[1]-1,dt[2],dt[3],dt[4],dt[5]);
        let event_start_date = eventStartDate.getDate();
        let event_end_date   = eventEndDate.getDate();

        let first = true;

        for (let dayIndex = event_start_date; dayIndex <= event_end_date; dayIndex++) {

            if (dayIndex < 10) {
                dayIndex = '0' + dayIndex;
            }

            let $calendar_day    = $calendar_days.find('a:contains(' + dayIndex + ')').parent().find('.events');
            let $rendered_event = $calendar_days.find('div[data-id=' + event.id + ']');

            let top = null;

            // If this event has been rendered before, then use it's current left position
            if ($rendered_event.length) {
                top = $rendered_event.first().css('top');
            // Otherwise, give it a top position based on the number of events currently rendered for the block
            } else {
                let event_count = $calendar_day.find('.event').length;
                top = (event_count * 30) + 'px';
            }

            event.position_top = top;

            $calendar_day.append(build_event_markup(event));

            if (first) {
                event.title += ' (cont\'d)';
            }

            first = false;
        }

    }

    // Load Edit Page On Calendar Event Click
    $calendar_days.find('.event').on('click', function (e){
        e.stopPropagation();
        const $this = $(this);
        switch ($this.attr('data-event')) {
        case 'reminder':
            window.location.href = `${URLS.backend}leads/lead/reminders/?id=${parseInt($this.attr('data-lead'))}&edit=${parseInt($this.attr('data-id'))}`;
            break;
        default:
            window.location.href = `${URLS.backend}calendar/event/view/?id=` + parseInt($this.attr('data-id'));
            break;
        }
    });
    $calendar_days.find('.event.editable').on('click', function (e) {
        calendar_event_click($(this), e);
    });
};

/**
 * Adds the collection of event objects to the calendar's day view
 * @param array events
 * @return void
 */
let render_day_events = function (events) {

    let count = events.length;
    let $calendar_day = $('#calendar_day');
    let markup = '';

    for (let i = 0; i < count; i++) {
        // If All Day Event
        if (events[i].allDay === true) {
            markup += build_event_markup(events[i]);
        }
    }

    // Add All Day Events
    $calendar_day.find('#all_day').append(markup);

    // Add Timed Events
    for (let eventIndex = 0; eventIndex < count; eventIndex++) {

        let event = events[eventIndex];

        // Skip All Day Events As They Are Already Rendered
        if (event.allDay === true) continue;

        let dt = event.formatted_start.split(/[^\d]+/);
        let eventStartDate = new Date(dt[0],dt[1]-1,dt[2],dt[3],dt[4],dt[5]);
        dt = event.formatted_end.split(/[^\d]+/);
        let eventEndDate = new Date(dt[0],dt[1]-1,dt[2],dt[3],dt[4],dt[5]);

        let event_start_hours = eventStartDate.getHours();
        let event_end_hours   = eventEndDate.getHours();

        let event_start_minutes = eventStartDate.getMinutes();
        let event_end_minutes   = eventEndDate.getMinutes();

        let first = true;

        for (let hourIndex = event_start_hours; hourIndex <= event_end_hours; hourIndex++) {

            // TODO Base magic 30 number off of TIME_INTERVAL
            // If not first event render pass
            if (!first) {
                // set the start minutes to 0 if the event's starting minutes is at 30 or higher set to 0
                if (event_start_minutes >= 30) {
                    event_start_minutes = 0;
                }

                // If the event hours is less than the end, set to interval
                if (hourIndex < event_end_hours) {
                    event_end_minutes = 30;
                }

                // If this is last hour of the event, set the end minutes to the actual event's end minutes
                if (hourIndex == event_end_hours) {
                    event_end_minutes = eventEndDate.getMinutes();
                }
            }

            let ampm = (hourIndex >= 12 ? 'pm' : 'am');
            let clock_hour = hourIndex;
            if (hourIndex > 12) {
                clock_hour = hourIndex - 12;
            } else if (hourIndex == 0) {
                clock_hour = 12;
            }

            for (let minuteIndex = event_start_minutes; minuteIndex <= event_end_minutes; minuteIndex += 30) {
                // Add To Top Of The Hour
                if (minuteIndex < 30) {
                    let $calendar_hour = $calendar_day.find('#' + clock_hour + '\\:00-' + ampm);
                    $calendar_hour.append(build_event_markup(event));
                // Add To Half Hour
                } else {
                    let $calendar_hour = $calendar_day.find('#' + clock_hour + '\\:30-' + ampm);
                    $calendar_hour.append(build_event_markup(event));
                }

                if (first) {
                    event.title += ' (cont\'d)';
                }

                first = false;
            }

        }
    }


    // Load Event's Edit Page On Calendar Event Click
    $calendar_day.find('.event.editable').on('click', function (e) {
        calendar_event_click($(this), e);
    });
};

let calendar_event_click = function ($this, e) {
    e.stopPropagation();

    switch ($this.attr('data-event')) {
    case 'reminder':
        window.location.href = `${URLS.backend}leads/lead/reminders/?id=${parseInt($this.attr('data-lead'))}&edit=${parseInt($this.attr('data-id'))}`;
        break;
    default:
        window.location.href = `${URLS.backend}calendar/event/edit/?id=` + parseInt($this.attr('data-id'));
        break;
    }
};

/**
 * Adds the collection of event objects to the calendar's list view
 * @param array events
 * @return void
 */
let render_list_events = function (agenda) {
    let $list = $('#calendar-agenda');

    $list.append(agenda);
};

// Calendar Event Date Range
let params = {};
if ($calendar.view != 'day') {
    params['start'] = $calendar.year + '-' + $calendar.month + '-1';
    params['end']   = $calendar.year + '-' + $calendar.month + '-' + $calendar.days;
} else {
    params['start'] = $calendar.year + '-' + $calendar.month + '-' + $calendar.day;
    params['end']   = $calendar.year + '-' + $calendar.month + '-' + $calendar.day;
}
params['view']  = $calendar.view;

// Load Events
let load_events = function () {
    xhr && xhr.abort();
    xhr = $.ajax({
        url      : `${URLS.backendAjax}calendar/events.php?load`,
        type     : 'POST',
        data     : params,
        dataType : 'json',
        success  : function (json) {
            switch ($calendar.view) {
            case 'day':
                // Remove Old Events
                $('#calendar_day').find('td').text('');
                render_day_events(json.events);
                break;
            case 'list':
                // Remove Old Events
                $('#calendar-agenda').text('');
                render_list_events(json.agenda);
                break;
            default:
                // Remove Old Events
                $calendar_days.find('.event').remove();
                // Add Events To Calendar
                render_calendar_events(json.events);
                break;
            }
        }
    });
};

// Load Events
load_events();

// AJAX Event Filtering
$('#calendar select[name="event_filters"]').selectize({
    onChange: function (values) {
        params['filters'] = values;
        load_events();
    }
});

// Agent Filter
$('#calendar select[name="agent"]').on('change', function () {
    params['agent'] = $(this).val();
    load_events();
});
