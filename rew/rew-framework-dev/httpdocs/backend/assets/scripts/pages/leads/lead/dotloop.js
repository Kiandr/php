// Update Status Dropdown Based on Current Value of Transaction Type Dropdown
$('#new-loop-form select[name="loop_transaction_type"]').on('change', function() {
    let type = $(this).val(),
        $statuses = $('#new-loop-form select[name="loop_status"]');
    $statuses.find('option').each(function() {
        let $this = $(this);
        $statuses.val('');
        if ($this.data('transaction-type') === type) {
            $this.attr('disabled', false);
            $this.attr('hidden', false);
        } else {
            $this.attr('disabled', true);
            $this.attr('hidden', true);
        }
    });
}).trigger('change');
//Update Template Dropdown Based on Current Value of Target Profile Dropdown
$('#new-loop-form select[name="profile_id"]').on('change', function() {
    let profile_id = $(this).val(),
        $templates = $('#new-loop-form select[name="template_id"]');
    $templates.find('option').each(function() {
        let $this = $(this);
        $templates.val('');
        if ($this.data('profile-id') == profile_id) {
            $this.attr('disabled', false);
            $this.attr('hidden', false);
        } else {
            $this.attr('disabled', true);
            $this.attr('hidden', true);
        }
    });
}).trigger('change');
// Toggle New/Existing Loop Forms
$('#display-new-loop-form').on('click', () => {
    $('#new-loop-form').show();
    $('#existing-loop-form').hide();
});
$('#display-existing-loop-form').on('click', () => {
    $('#new-loop-form').hide();
    $('#existing-loop-form').show();
});

//DotLoop Rate Limit Exceeded - Timer
const $dotloop_rl_timer = $('#dotloop-rate-timer');
if ($dotloop_rl_timer.length) {
    let rlt_remaining = $dotloop_rl_timer.data('remaining');
    let rlt_interval = setInterval(() => {
        rlt_remaining = rlt_remaining - 1;
        if (rlt_remaining > 0) {
            $dotloop_rl_timer.html(' in ' + rlt_remaining + ' seconds');
        } else {
            $dotloop_rl_timer.empty();
            clearInterval(rlt_interval);
        }
    }, 1000);
}