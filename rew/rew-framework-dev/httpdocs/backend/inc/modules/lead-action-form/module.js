import URLS from 'constants/urls';
import showErrors from 'utils/showErrors';
import showSuccess from 'utils/showSuccess';
import 'jquery-ui-timepicker-addon';

// MLS listing autocomple field
import 'common/mls-autocomplete';

// Remove hidden module class
$('#quick_note').removeClass('hidden');

// Tabset to switch forms
$('#quick_note .tabbed').tabs({
    beforeActivate: function (event, ui) {
        $(ui.newPanel).removeClass('hidden');
    }
});

// Handle form submission
$('#quick_note form').on('submit', function () {
    const $form = $(this);
    const $msg = $form.find('.action-message');
    const $submit = $form.find('[type="submit"]');
    const loadingText = $submit.data('loading') || 'Loading...';
    const action = $form.find('input[name="action"]').val();
    if (!action || action.length === 0) return true;
    $submit.prop('disabled', true).data('text', $submit.text()).text(loadingText);
    $msg.html('').removeClass('error success');
    $.ajax({
        url: `${URLS.backendAjax}json.php?action=${action}`,
        type: 'POST',
        dataType: 'json',
        data: $form.serialize()
    }).done(json => {
        $submit.prop('disabled', false).text($submit.data('text'));
        if (json.success) {
            $form.trigger('reset');
            showSuccess(json.success);
        }
        if (json.errors) {
            showErrors(json.errors);
        }
    }).fail(() => {
        $submit.prop('disabled', false).text($submit.data('text'));
        showErrors(['An unexpected error occurred.']);
    });
    return false;
});

// Date time picker for creating reminders
const datepicker = $('#quick_note_reminder input[name="timestamp"]').datetimepicker({
    showButtonPanel: true,
    changeMonth: true,
    changeYear: true,
    dateFormat: 'DD, MM d, yy',
    ampm: true,
    separator: ' ',
    timeFormat: 'h:mmtt',
    stepMinute: 15,
    onSelect: function () {
        $('#quick_note_reminder a.custom').addClass('active')
            .siblings('.quickpick').removeClass('active');
    }
});

// Quick pick for reminder dates
$('#quick_note_reminder button.quickpick').on('click', function () {
    const $this = $(this);
    $this.addClass('active').siblings('.quickpick').removeClass('active');
    datepicker.datetimepicker('setDate', new Date($this.data('timestamp') * 1000));
});

// Custom date picker for reminders
$('#quick_note_reminder a.custom').on('click', function () {
    $(this).addClass('active').siblings('.quickpick').removeClass('active');
    datepicker.trigger('blur').datepicker('show');
    return false;
});

// Toggle notification message when adding saved listings for leads
$('#quick_note_listing input[name="notify"]').on('change', function () {
    $('#quick_note_listing_message').toggleClass('hidden', !this.checked);
});