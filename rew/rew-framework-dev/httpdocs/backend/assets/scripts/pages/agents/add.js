// Add/remove button to delete uploaded agent photo
const $photoField = $('input[name="agent_photo"]').on('change', function () {
    if (this.files && this.files.length > 0) {
        $photoDelete.insertAfter($photoField);
    } else {
        $photoDelete.detach();
    }
});

// Button to delete photo from agent form
const $photoDelete = $('<a class="btn btn--ghost">' +
    '<svg class="icon icon-trash mar0">' +
        '<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use>' +
    '</svg>' +
'</a>').on('click', function () {
    $photoDelete.detach();
    $photoField.val('');
});

// Agent form fields
var $agent_cms = $('#agent-cms');
var $mls_boards = $('input[name="mls_boards"]');
var $agent_id_fields = $('input[name^="feeds_agent"]');

// If There Is An Empty Required Agent ID,
// Then Agent Subdomain Request Can't Be Submitted.
var check_agent_id_fields = () => {
    $mls_boards.val('true');
    $agent_id_fields.each( function () {
        if ($(this).val() == '' && $(this).prop('required')) {
            $mls_boards.val('');
            // break
            return false;
        }
    });
    if ($mls_boards.val() == 'true') {
        var $fields = $agent_id_fields.not('[required]');
        if ($fields.length == $agent_id_fields.length) $mls_boards.val('');
    }
};

// Toggle Agent Subdomain
$('input[name="cms"]').bind('change', function () {
    const enabled = this.value === 'true' && this.checked;
    $agent_cms.toggleClass('hidden', !enabled);
    $mls_boards.prop('required', enabled);
    $cms_link.prop('required', enabled);
});

// Check Whether Agent Subdomain Request Can Be Made Whenever An Agent ID Field Changes
$('input[name^="feeds_agent"]').on('change keyup keypress', function () {
    check_agent_id_fields();
});

// Toggle Require Agent ID
$('input[name^="requested_feeds"]').on('change', function () {
    var id = $(this).attr('id');
    var feed = id.split('_')[2];
    var $feed = $('input[name="feeds_agent[' + feed + ']"]');
    if ($(this).is(':checked')) {
        $feed.prop('required', true);
        $feed.removeClass('hidden');
    } else {
        $feed.removeAttr('required');
        $feed.addClass('hidden');
    }
    check_agent_id_fields();
});

// Toggle agent website link field
const $cms_link = $('input[name="cms_link"]').bind('keyup', function () {
    var $this = $(this), value = $this.val().replace(' ', '-'),
        chars = ' :;*&^%$#@!{}][,<>?/\|+=()`~\'"\\', stripped = '',
        i = 0, l = value.length, link = $this.data('link');
    for (i; i < l; i++){
        if (chars.indexOf(value.charAt(i)) == -1){
            stripped += value.charAt(i);
        }
    }
    $this.val(stripped);
    $('#account-link').text(link.replace('*', stripped));
});

// Toggle Auto-OptOut
const $auto = $('input[name="auto_rotate"], input[name="auto_assign_admin"]').on('change', function () {
    var $this = $(this), value = $this.val();
    if (value == 'true' && this.checked) {
        $('input[name="auto_optout"]').prop('disabled', false);
    } else if ($auto.filter('[value="true"]:checked').length === 0) {
        $('input[name="auto_optout"]').prop('disabled', true)
            .filter('[value="false"]').prop('checked', true);
    }
});
