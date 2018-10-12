// Enable actions when results checked
const $agentActions = $('.group_actions .btn');
const $agentCheckboxes = $('input[name="agents[]"]');
$agentCheckboxes.on('change', function () {
    const checked = $agentCheckboxes.filter(':checked').length;
    $agentActions.prop('disabled', checked < 1);
});

// Handle email action's button click
$('#agents-email').on('click', function () {
    if ($agentCheckboxes.filter(':checked').length < 1) {
        alert('First, select the agent(s) you\'d like to perform this action on.');
        return false;
    }
    $agentCheckboxes.closest('form').trigger('submit');
});
