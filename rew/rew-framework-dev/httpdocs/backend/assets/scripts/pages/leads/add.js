import groupPicker from 'common/groupPicker';

groupPicker('select[name="groups[]"]');

// Toggle reason for rejection and Shark Tank
const $reason = $('#rejectwhy_field'),
    $in_tank = $('#in_shark_tank_field'),
    $lead_status = $('#lead_status');
$lead_status.on('change', function () {
    const rejected = $(this).val() === 'rejected',
        unassigned = $(this).val() === 'unassigned';
    $reason.find('input').prop('required', rejected);
    $reason.toggleClass('hidden', !rejected);
    $in_tank.find('select').prop('disabled', !unassigned);
    $in_tank.toggleClass('hidden', !unassigned);
});

// Disable Un-Assigned Option if Agent Other Than Super Admin is Assigned
const $assign_agent = $('#assign-agent');
$assign_agent.on('change', function () {
    const agent_id = $(this).val();
    if (agent_id !== '1') {
        if ($lead_status.val() === 'unassigned') {
            $lead_status.find('option[value="pending"]').attr('selected', true);
            $lead_status.trigger('change');
        }
        $lead_status.find('option[value="unassigned"]').attr('disabled', true);
    } else {
        $lead_status.find('option[value="unassigned"]').attr('disabled', false);
    }
});

// Custom Date Range Filter
const $fields = $('#custom-list');
$fields.find('input[placeholder="yyyy-mm-dd"]').datepicker({
    dateFormat: 'yy-mm-dd',
    showButtonPanel: true,
    changeMonth: true,
    changeYear: true,
    onSelect: function() {
        this.setAttribute('placeholder', '');
    }
});