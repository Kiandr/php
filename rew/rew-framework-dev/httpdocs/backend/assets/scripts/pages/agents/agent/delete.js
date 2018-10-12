import showErrors from 'utils/showErrors';

// Import form
const $form = $('form#agent_delete');

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
const $progressBarInstance = $progressBar.find('.ui-progressbar-value');
const $progressMsg = $progressBar.find('.ui-progressbar-text');
const $errors = $('#import-errors');
const $success = $('#import-success');
const $statusTitle = $('#import-status-title');
const $nextImportSteps = $('#next-import-steps');

// AJAX Form Submit
$form.on('submit', function () {

    // Only allow form to submit once
    if ($form.hasClass('submitted')) return false;
    $('#agent-delete-buttons').addClass('hidden');
    $form.addClass('submitted');
    window.scrollTo(0, 0);

    // Import in Progress, Warn on Page Leave
    $(window).on('beforeunload', function () {
        return 'Agent delete in progress. Are you sure you want to leave this page?';
    });

    // Are there leads to process?
    const leadsCount = $form.find('input[name="leads_count"]').val();

    // Process Leads
    if (leadsCount) {

        $form.find('select[name="agent"]').attr('disabled', true);
        $form.find('select[name="status"]').attr('disabled', true);

        const leadsPerRequest = 1000;
        let t = Math.ceil(leadsCount / leadsPerRequest);
        for (let i = 1; i <= t; i++) {

            // Queue AJAX Request
            ajaxManager.addReq({
                url: '?',
                type: 'POST',
                dataType: 'json',
                data: {
                    reassign: true,
                    ajax: true,
                    id: $form.find('input[name="id"]').val(),
                    agent: $form.find('select[name="agent"]').val(),
                    status: $form.find('select[name="status"]').val(),
                    limit: leadsPerRequest,
                    leads_count: leadsCount
                },
                success: function (data, status, xhr) { // eslint-disable-line no-unused-vars

                    // Reassignments Complete
                    if (i == t) {
                        // Finish progress bar
                        $progressBarInstance.stop();
                        $progressBarInstance.show().animate({
                            width: '100%'
                        });
                        // Show success rate
                        $success.removeClass('hidden').append('<p>Successfully reassigned ' + leadsCount + ' leads.<p>');
                    } else {
                        // Adjust progress bar to correct width
                        $progressBarInstance.stop();
                        $progressBarInstance.show().animate({
                            width: ((i / t) * 100) + '%'
                        });
                        // Start on next segment
                        $progressBarInstance.show().animate({
                            width: (((i + 1) / t) * 100) + '%'
                        }, {duration: 60000});
                    }

                    // Update Text
                    if (i < t) {
                        $progressMsg.html('Reassigning Leads');

                    } else {

                        // Import Complete
                        $(window).off('beforeunload');
                        $progressMsg.html('Reassignments Complete');
                        $statusTitle.hide();
                        $nextImportSteps.removeClass('hidden');

                    }

                    // Error Notifications
                    if (data.errors) {
                        $errors.removeClass('hidden').append('<p>' + data.errors.join('</p><p>') + '</p>');
                    }

                },
                error: function (xhr, status, error) { // eslint-disable-line no-unused-vars

                    // Show Success Message
                    showErrors(['Connection error has occurred, lead reassignments halted.'], undefined, {
                        close: true,
                        expires: 10000
                    });

                    ajaxManager.stop();
                }
            });

        }

        // Update progress text
        $progressMsg.html('Reassigning Leads');

        // Show progress bar
        $progress.removeClass('hidden');
        // Start and animate to first segment
        $progressBarInstance.show().animate({
            width: ((1 / t) * 100) + '%'
        }, {duration: 60000});

    }

    // Queue AJAX Request
    ajaxManager.addReq({
        url: '?',
        type: 'POST',
        dataType: 'json',
        data: {
            delete: true,
            ajax: true,
            id: $form.find('input[name="id"]').val()
        },
        success: function (data, status, xhr) { // eslint-disable-line no-unused-vars

            // Import Successful, Remove from List
            if (data.success) {
                $success.removeClass('hidden').append('<p>' + data.success.join('</p><p>') + '</p>');
            }

            // Error Notifications
            if (data.errors) {
                $errors.removeClass('hidden').append('<p>' + data.errors.join('</p><p>') + '</p>');
            }

        },
        error : function (xhr, status, error) { // eslint-disable-line no-unused-vars

            // Show Success Message
            showErrors(['Connection error has occurred, agent delete halted.'], undefined, {
                close: true,
                expires: 10000
            });

            ajaxManager.stop();
        }
    });

    // Next AJAX request
    ajaxManager.run();
    return false;

});