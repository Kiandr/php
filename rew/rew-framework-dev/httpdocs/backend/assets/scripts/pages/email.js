import URLS from 'constants/urls';
import showErrors from 'utils/showErrors';
import 'pickadate/lib/picker.date';
import 'pickadate/lib/picker.time';

// MLS listing autocomple field
import 'common/mls-autocomplete';

//Get Timeline For Redirecting
$('input[name="timeline_id"]').val(sessionStorage.getItem('timeline_id'));

// Date Picker
var $picker = $('#datePicker').pickadate({
    format: 'dddd, mmmm d, yyyy',
    formatSubmit: 'Y-m-d',
    min: new Date()
});
var picker = $picker.pickadate('picker');

// Time Picker
var $pickTime = $('#timePicker').pickatime({
    interval: 15,
    formatSubmit: 'hh:mm TT',
    min: new Date()
});

var pickTime = $pickTime.pickatime('picker');

picker.on('set', function(event) {
    if (event.select) {
        var time = new Date(picker.get('value'));
        var today = new Date();
        if(time.getDate() === today.getDate()){
            pickTime.set('min', new Date());
        } else {
            pickTime.set('min', new Date(picker.get('value')));
        }
    }
});


// Toggle date/time for delayed emails
$('input[name="delay"]').on('change', function () {
    $('#email_delay').toggleClass('hidden', !this.checked);
});

// Show CC email field
$('#addCC').on('click', function () {
    $('#emailCC').removeClass('hidden').find('input').trigger('focus');
    $(this).remove();
    return false;
});

// Show BCC email field
$('#addBCC').on('click', function () {
    $('#emailBCC').removeClass('hidden').find('input').trigger('focus');
    $(this).remove();
    return false;
});

// Allow up to 3 email attachments
const attachments = 'input[name="attachments[]"]';
$('#email-attachments').on('change', attachments, function () {
    const $this = $(this);
    if ($(attachments).length < 3) {
        const input = $this.clone().val('');
        $this.after(input);
    }
});

// Email message field
const $message = $('#email_message');

// Toggle HTML email message
const $toggle = $('input[name="is_html"]');
$toggle.on('change', function () {
    const editor = $message.tinymce();
    if (this.checked && this.value === 'false') {
        if (editor) editor.remove();
        $message.addClass('off');
    } else if (!editor) {
        $message.removeClass('off').setupTinyMCE();
    }
});

// Force HTML message if using template
const $template = $('select[name="tmp_id"]').on('change', function () {
    const editor = $message.tinymce();
    if (this.value && this.value.length > 0) {
        if (!editor) $message.removeClass('off').setupTinyMCE();
        $toggle.prop('disabled', true).prop('checked', true);
        $toggle.filter('[value="true"]').prop('checked', true);
    } else {
        $toggle.prop('disabled', false);
    }
});

// Choose form letter document and load into editor
$('select[name="doc_id"]').on('change', function () {
    const $this = $(this);
    const value = $this.val();
    const editor = $message.tinymce();
    const message = editor ? editor.getContent() : $message.val();
    const confirmChange = message.length !== 0;
    let loadDoc = !confirmChange;
    if (confirmChange) {
        loadDoc = confirm(
            'Changing Pre-Built Emails will erase any changes to your message\n.'
            + 'Do you wish to continue?'
        );
    }
    if (loadDoc) {
        $.ajax({
            url: `${URLS.backendAjax}getEmail.php`,
            type: 'get',
            dataType: 'json',
            data: {
                id: value
            },
            success: function (data) {
                if (data.returnCode == 200) {
                    const doc = data.document;

                    // Force HTML email selection
                    if (data.is_html === 'true' || $template.val() !== '') {
                        $message.removeClass('off').setupTinyMCE();
                        $toggle.filter('[value="true"]').prop('checked', true);
                        $message.tinymce().setContent(doc);

                    } else {

                        // Plaintext message selected
                        $toggle.filter('[value="false"]').prop('checked', true);
                        $toggle.prop('disabled', false);
                        if (editor) editor.remove();
                        $message.val(doc);

                    }

                // Display error message
                } else if (data.message) {
                    alert(data.message);

                }
            }
        });
    }
});

// Insert MLS listing into email message
const $insertListing = $('#insert-listing').on('click', function () {
    insertListingPreview($mlsNumber.val());
});

// Insert listing on ENTER
const $mlsNumber = $('input[name="mls_number"]').bind('keypress', function (e) {
    if (e.which !== 13) return true;
    $insertListing.trigger('click');
    e.preventDefault();
    return false;
}).on('autocompleteselect', function (event, ui) {
    insertListingPreview(ui.item.value);
});

// Insert MLS listing into email message
const insertListingPreview = (mlsNumber)  => {
    const $feed = $('select[name="feed"]');
    $.ajax({
        url: `${URLS.backendAjax}json.php?searchListings`,
        type: 'GET',
        dataType: 'json',
        data: {
            mls_number: mlsNumber,
            feed: $feed.val()
        },
        success: function(json) {
            const listings = json.listings;
            if (listings && listings.length > 0) {
                listings.forEach(function (listing) {
                    const editor = $message.tinymce();
                    var editorContent = editor ? editor.getContent() : $message.val();
                    if (editorContent.search('<p id="listingsEnd">&nbsp;</p>') > -1) {
                        editorContent = editorContent.replace('<p id="listingsEnd">&nbsp;</p>', '<br>' + listing.preview);
                        // check if the listing feed's disclaimer is already there
                        if (editorContent.search('<p id="disclaimer--' + listing.feed + '">&nbsp;</p>') <= -1) {
                            editorContent = editorContent.replace('<p id="disclaimersEnd">&nbsp;</p>', listing.disclaimer);
                        }
                    } else {
                        editorContent = editorContent + listing.preview + listing.disclaimer;
                    }
                    if (editor) {
                        editor.setContent(editorContent);
                    }
                    if (!editor) {
                        $message.val(editorContent);
                    }
                });
            }
            $mlsNumber.val('').trigger('blur');
            if (json.errors) showErrors(json.errors);
        }
    });
};