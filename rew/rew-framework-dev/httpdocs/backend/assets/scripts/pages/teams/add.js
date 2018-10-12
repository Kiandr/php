import labelPicker from 'common/labelPicker';

// Enable team label's style picker
labelPicker('select[name="style"]');

// Team form fields
var $team_subdomain = $('#team-subdomain');
var $mls_boards = $('input[name="mls_boards"]');
var $team_id_fields = $('input[name^="feeds_team"]');

// If There Is An Empty Required Team ID,
// Then Team Subdomain Request Can't Be Submitted.
var check_team_id_fields = () => {
    $mls_boards.val('true');
    $team_id_fields.each( function () {
        if ($(this).val() == '' && $(this).prop('required')) {
            $mls_boards.val('');
            // break
            return false;
        }
    });
    if ($mls_boards.val() == 'true') {
        var $fields = $team_id_fields.not('[required]');
        if ($fields.length == $team_id_fields.length) $mls_boards.val('');
    }
};

// Toggle team website
$('input[name="subdomain"]').on('change', function () {
    const enabled = this.value === 'true' && this.checked;
    $team_subdomain.toggleClass('hidden', !enabled);
    $subdomain_link.prop('required', enabled);
    $mls_boards.prop('required', enabled);
});

// Check Whether Team Subdomain Request Can Be Made Whenever An Team ID Field Changes
$('input[name^="feeds_team"]').on('change keyup keypress', function () {
    check_team_id_fields();
});

// Toggle Require Team ID
$('input[name^="requested_feeds"]').on('change', function () {
    var id = $(this).attr('id');
    var feed = id.split('_')[2];
    var $feed = $('input[name="feeds_team[' + feed + ']"]');
    if ($(this).is(':checked')) {
        $feed.prop('required', true);
        $feed.removeClass('hidden');
    } else {
        $feed.prop('required', false);
        $feed.addClass('hidden');
    }
    check_team_id_fields();
});

// Team website link preview
const $subdomain_link = $('input[name="subdomain_link"]').on('keyup', function () {
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

// Check Whether team Subdomain Request Can Be Made Whenever An team ID Field Changes
$('input[name^="feeds_team"]').on('change keyup keypress', function () {
    check_team_id_fields();
});

// Toggle Require team ID
$('input[name^="requested_feeds"]').on('change', function () {
    var id = $(this).attr('id');
    var feed = id.split('_')[2];
    var $feed = $('input[name="feeds_team[' + feed + ']"]');
    if ($(this).is(':checked')) {
        $feed.prop('required', true);
        $feed.removeClass('hidden');
    } else {
        $feed.removeAttr('required');
        $feed.addClass('hidden');
    }

    // Check Whether team Subdomain Request Can Be Made Whenever A Feed Is Checked/Unchecked
    check_team_id_fields();
});

// Add/remove button to delete uploaded team photo
const $photoField = $('input[name="team_photo"]').on('change', function () {
    if (this.files && this.files.length > 0) {
        $photoDelete.insertAfter($photoField);
    } else {
        $photoDelete.detach();
    }
});

// Button to delete photo from team form
const $photoDelete = $('<a class="btn btn--ghost">' +
    '<svg class="icon icon-trash mar0">' +
        '<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use>' +
    '</svg>' +
'</a>').on('click', function () {
    $photoDelete.detach();
    $photoField.val('');
});
