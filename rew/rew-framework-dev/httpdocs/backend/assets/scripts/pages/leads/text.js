// Load SMS attachment code
import 'common/attach-media';

// Toggle display of opt-out leads
$('#view-optout-leads').on('click', function () {
    const $leads = $('#optout-leads');
    const toggle = $leads.hasClass('hidden');
    $(this).text(`${toggle ? 'Show' : 'Hide'} Leads`);
    $leads.toggleClass('hidden', !toggle);
});
