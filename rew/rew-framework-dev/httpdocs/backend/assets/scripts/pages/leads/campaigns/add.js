import groupPicker from 'common/groupPicker';

groupPicker('select[name="groups[]"]');

// Use picker for campaign start date
$('input[name="starts"]').datepicker({
    showButtonPanel: false,
    changeMonth: true,
    changeYear: true,
    dateFormat: 'D, M. d, yy',
    minDate: 0
});

// Toggle campaign's sender details
$('input[name="sender"]').bind('change', function () {
    const custom = this.value === 'custom' && this.checked;
    $('#campaign-sender').toggleClass('hidden', !custom);
});

// Campaign email list
var $emails = $('#campaign-emails');

// Remove campaign email
$emails.on('click', 'a.delete', function () {
    if ($emails.find('.email').length == 1) {
        alert('You must have at least one campaign email.');
        return false;
    }
    if (confirm('Are you sure you want to remove this campaign email?')) {
        $(this).closest('.email').remove();
    }
    return false;
});

// Add new campaign email
var index = $emails.find('.email').length + 1;
$('#add-campaign-email').on('click', function () {
    var $email = $emails.find('.email').last().clone();
    $email.find(':input').each(function (i, input) {
        var $input = $(input), name = $input.prop('name').replace(/\d/g, index);
        $input.prop('name', name);
        if (name == 'emails[' + index + '][send_delay]') {
            $input.val(parseInt($input.val()) + 1);
        } else {
            $input.val('');
        }
        if (name == 'emails[' + index + '][id]') {
            $input.remove();
        }
    });
    index++;
    $email.appendTo($emails);
    return false;
});