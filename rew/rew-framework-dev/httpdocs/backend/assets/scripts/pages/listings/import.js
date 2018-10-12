import showSuccess from 'utils/showSuccess';
import showErrors from 'utils/showErrors';

// Import form
const $form = $('form.import');
const $submit = $form.find('button[type="submit"]');
const $return = $form.find('a.return');

// Check all
const $all = $('#check_all').on('change', function () {
    $submit.prop('disabled', !this.checked);
    $inputs.prop('checked', this.checked);
}).prop('checked', false);

// Check one
const $inputs = $('input[name="listings[]"]').on('change', function () {
    const count = $inputs.filter(':checked').length;
    $all.prop('checked', count == $inputs.length);
    $submit.prop('disabled', count < 1);
}).prop('checked', false);

// AJAX Manager @see http://stackoverflow.com/a/4785886/634569
const ajaxManager = (function() {
    let requests = [];
    return {
        addReq:  function(opt) {
            requests.push(opt);
        },
        removeReq:  function(opt) {
            if( $.inArray(opt, requests) > -1 )
                requests.splice($.inArray(opt, requests), 1);
        },
        run: function() {
            let self = this;
            if (requests.length) {
                const oriSuc = requests[0].complete;
                requests[0].complete = function () {
                    if (typeof oriSuc === 'function') oriSuc();
                    requests.shift();
                    self.run.apply(self, []);
                };
                $.ajax(requests[0]);
            } else {
                self.tid = setTimeout(function() {
                    self.run.apply(self, []);
                }, 1000);
            }
        },
        stop:  function() {
            requests = [];
            clearTimeout(this.tid);
        }
    };
}());

// Progress Data
const $progress = $('#progress');
const $progressBar = $progress.find('.progress').progressbar();
const $progressMsg = $progressBar.find('.ui-progressbar-text');
const $errors = $('#import-errors');
const $statusTitle = $('#import-status-title');
const $nextImportSteps = $('#next-import-steps');

// AJAX Form Submit
$form.on('submit', function () {

    // Only allow form to submit once
    if ($form.hasClass('submitted')) return false;
    $('#import-listings').addClass('hidden');
    $form.addClass('submitted');
    $submit.addClass('hidden');
    $return.attr('disabled', true).removeClass('hidden');
    window.scrollTo(0, 0);

    // Import in Progress, Warn on Page Leave
    $(window).on('beforeunload', function() {
        return 'Import in progress. Are you sure you want to leave this page?';
    });

    // Process Checked Inputs
    const checked = $inputs.filter(':checked'), t = checked.length;
    let imported = 0, successfulImports = 0;
    for (let i = 0; i < t; i++) {
        const input = checked[i];

        // Queue AJAX Request
        ajaxManager.addReq({
            url: '?submit',
            type: 'GET',
            dataType: 'json',
            data: {
                ajax: true,
                listings: [input.value],
                feed: $('input[name="feed"]').val()
            },
            context: input,
            success: function (data, status, xhr) { // eslint-disable-line no-unused-vars

                // Increment
                imported++;

                // Import Successful, Remove from List
                if (data.success) {

                    // Show Success Message
                    showSuccess([data.success.join('<br>')], undefined, {
                        close: true,
                        expires: 10000
                    });
                    
                    successfulImports++;
                }

                // Import Complete, Show success rate
                if (imported == t) {
                    const successCount = (successfulImports == t) ? 'all listings.' : successfulImports + ' of ' + t + ' listings.';

                    // Show Success Message
                    showSuccess(['Successfully imported ' + successCount], '', {
                        close: true,
                        expires: 10000
                    });

                    // Enable Return Button
                    $return.attr('disabled', false);
                }

                // Update Process
                const width = (imported < t ? ((imported / t) * 100) - 1 : 100) + '%';
                $progressBar.find('.ui-progressbar-value').show().animate({
                    width: width
                }, { queue : false });

                // Update Text
                if (imported < t) {
                    $progressMsg.html('Importing Listing #' + (imported + 1) + ' of ' + t  + '&hellip;');

                } else {

                    // Import Complete
                    $(window).off('beforeunload');
                    $progressMsg.html('Import Complete');
                    $statusTitle.hide();
                    $nextImportSteps.removeClass('hidden');

                }

                // Error Notifications
                if (data.errors) {
                    $errors.removeClass('hidden').append('<p>' + data.errors.join('</p><p>') + '</p>');
                }

            },
            error : function (xhr, status, error) { // eslint-disable-line no-unused-vars

                // Show Success Message
                showErrors(['Connection error has occurred, listing import halted.'], undefined, {
                    close: true,
                    expires: 10000
                });

                ajaxManager.stop();
                // Enable Return Button
                $return.attr('disabled', false);
            }
        });

    }

    // Update progress text
    $progressMsg.html('Importing Listing #' + (imported + 1) + ' of ' + t  + '&hellip;');

    // Update progress bar
    $progress.removeClass('hidden');
    $progressBar.find('.ui-progressbar-value').show().animate({
        width: (t === 1 ? 50 : ((1 / t) * 100) / 2)  + '%'
    }, { queue : false });

    // Next AJAX request
    ajaxManager.run();
    return false;

});