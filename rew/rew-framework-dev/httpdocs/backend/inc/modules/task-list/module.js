import URLS from 'constants/urls';
import showErrors from 'utils/showErrors';
import groupPicker from 'common/groupPicker';

groupPicker('select[name="groups[]"]');

const $taskList = $('#task-list');

// Toggle control for displaying task description/info
$taskList.find('.expand-task').click(function () {
    var $toggle = $(this);
    var task_id = $toggle.data('task');

    if ($toggle.hasClass('open')) {
        $toggle.removeClass('open');
        $toggle.find('i').removeClass('icon-minus-sign').addClass('icon-plus-sign');
        $('.task-content.task-' + task_id).addClass('hidden');
    } else {
        $toggle.addClass('open');
        $toggle.find('i').removeClass('icon-plus-sign').addClass('icon-minus-sign');
        $('.task-content.task-' + task_id).removeClass('hidden');
    }

    return false;
});

// Handle modal task action (Snooze, Dismiss, Complete) forms
$taskList.find('.task-action').click(function () {

    const task_data = $(this).parents('.actions').data('task');
    const action = $(this).data('action');
    let $form;
    let title;

    if (action == 'snooze') {
        $form = $('#modal-forms #snooze-form');
        title = 'Snooze Task "' + task_data.name + '"';
    } else if (action == 'dismiss') {
        $form = $('#modal-forms #dismiss-form');
        title = 'Dismiss Task "' + task_data.name + '"';
    } else if (action == 'complete') {
        $form = $('#modal-forms #complete-form');
        title = 'Complete Task "' + task_data.name + '"';
    } else if (action == 'note') {
        $form = $('#modal-forms #note-form');
        title = 'Add Note for Task "' + task_data.name + '"';
    }

    // Set up modal window
    $form.clone().dialog({
        width    : 600,
        dialogClass : 'task-action-dialog',
        position : {my : 'top', at : 'center', of : window, offset: '0 -200'},
        modal    : true,
        autoOpen : true,
        title    : title,
        open     : function () {
            const $this = $(this);
            $this.find('input[name=task]').val(task_data.id);
            $this.find('input[name=user]').val(task_data.user);
            $this.find('.cancel-button').click(function () {
                $this.dialog('close');
                return false;
            });
        },
        close : function () {
            $(this).dialog('destroy').remove();
        }
    });

});

// Task Processing Popups
$taskList.find('.process-task').click(function (evt) {
    const $this = $(this);
    const gid = $this.attr('data-gid');
    const task_id = $this.attr('data-tid');
    const task_aid = $this.attr('data-aid');
    const task_uid = $this.attr('data-uid');
    const task_type = $this.attr('data-type');
    const $form = $('#process-form');
    let title = 'Processing Task';
    let task_note = '';
    let form_content;
    let extra_ids;
    let groups;
    let action;

    // Task-Type Specific IDs
    if ($this.attr('data-extra-ids')) {
        extra_ids = $this.attr('data-extra-ids').split(',');
    }
    if ($this.attr('data-groups')) {
        groups = $this.data('groups');
    }

    // Prepare Form Contents
    $form.find('input[name="lead"]').val(task_uid);

    // Clear Form Task-Specific Content
    $form.find('.form-contents').empty();

    // Modal to handle email task
    if (task_type == 'Email' || task_type == 'Text' || task_type == 'Search') {

        var task_page = '?id=' + task_uid + ((task_type != 'Search') ? '&popup' : '') + '&shortcut&task=' + task_id + '&agent=' + task_aid,
            $task_dialog;

        $task_dialog = $('<div style="overflow: hidden;"></div>')
            .html('<iframe style="border: 0px;" src="' + task_page + '" width="100%" height="100%"></iframe>')
            .dialog({
                autoOpen: false,
                modal: true,
                height: 625,
                width: 800,
                resizable: false,
                title: task_type + ' Task',
                close : function () {
                    $(this).dialog('destroy').remove();
                }
            });
        $task_dialog.dialog('open');

        return;

    // Generate Form Field Markup Unique to Task Types
    } else if (task_type == 'Call') {
        action = 'call';
        title = 'Logging Call';

        form_content = `<section>
            <div class="field">
                <label class="field__label">Call Details</label>
                <select class="w1/1" name="type">'
                    <option value="">Call Outcome&hellip;</option>
                    <option value="call">Talked to Lead</option>
                    <option value="attempt">Attempted</option>
                    <option value="voicemail">Voicemail</option>
                    <option value="invalid">Wrong Number</option>
                </select>
            </div>
            <div class="field">
                <textarea class="w1/1" name="details" cols="24" rows="4" placeholder="Call Details"></textarea>
            </div>
            <div class="field">
                <label class="field__label">Task Note</label>
                <textarea class="w1/1" name="note" cols="24" rows="4"></textarea>
            </div>
        </section>`;

    } else if (task_type == 'Listing') {

        action = 'listing';
        title = 'Recommending Listing';

        form_content = `<section>
            <p class="text">Listings that are recommended for leads will show up in their lead dashboard.</p>
            <div class="field">
                <label class="field__label">Listing MLS&reg; # or Street Address</label>
                <input class="autocomplete listing w1/1" name="mls_number" value="" placeholder="Enter MLS&reg; # or Street Address" required>
            </div>
            <div class="field">
                <label class="field__label"><input type="checkbox" name="notify" value="Y" checked> Send Listing Email to Lead</label>
                <textarea class="w1/1" name="message" cols="24" rows="6" placeholder="Include this Message&hellip;"></textarea>
            </div>
            <div class="field">
                <label class="field__label">Task Note</label>
                <textarea class="w1/1" name="note" cols="24" rows="4"></textarea>
            </div>
        </section>`;

        $(document).on('click', '.recommend-listing input[name="notify"]', function() {
            const $this = $(this);
            const $message = $this.parent('label').siblings('textarea[name="message"]');
            if ($this.is(':checked')) {
                $message.show().prop('disabled', false);
            } else {
                $message.hide().prop('disabled', true);
            }
        });

        // MLS Listing Picker
        $(document).on('keydown', '.autocomplete.listing', function () {
            $(this).autocomplete({
                source: function (request, response) {
                    var $el = this.element,
                        $feed = $el.parents('div').first().find('select[name="feed"]'),
                        feed = $feed.length ? $feed.val() : '';

                    $.getJSON('/idx/inc/php/ajax/json.php?limit=15&search=search_listing', {
                        q : request.term,
                        'feed' : feed,
                        cache: false
                    }, function (data) {
                        var parsed = [], rows = data.options ? data.options : [];
                        var regex = new RegExp('(?![^&;]+;)(?!<[^<>]*)(' + request.term.replace(/([\^\$\(\)\[\]\{\}\*\.\+\?\|\\])/gi, '\\$1') + ')(?![^<>]*>)(?![^&;]+;)', 'gi');
                        for (var i = 0; i < rows.length; i++) {
                            var opt = rows[i];
                            opt.image = opt.image ? opt.image.replace('http://', '/thumbs/60x60/') : '/thumbs/60x60/img/404.gif';
                            opt.title = opt.title.replace(regex, '<mark>$1</mark>');
                            parsed.push(opt);
                        }
                        response(parsed);
                    });
                },
                focus: function () {
                    return false;
                }
            }).data('autocomplete')._renderItem = function (ul, item) {
                var $item = $('<li class="listing">').data('item.autocomplete', item);
                $item.append('<a>'
                    + '<img src="' + item.image + '" border="0">'
                    + '<strong>' + item.title + '</strong><br>'
                    + (item.lines ? '<span>' + item.lines.join('</span><br><span>') + '</span>' : '')
                + '</a>');
                return $item.appendTo(ul);
            };
        });

    } else if (task_type == 'Group' && extra_ids.length > 0) {

        action = 'groups';
        title = 'Assigning Group' + (extra_ids.length > 1 ? 's' : '');

        form_content = '';
        const options = groups.reduce(function(result, group) {
            return result + '<option data-data=\'{ "style": "' + group.style + '" }\' value="' + parseInt(group.id) + '"' + (group.selected ? ' selected' : '') + '>' + group.name + '</option>';
        }, '');
        form_content += '<input type="hidden" name="auto_assign_groups" value="Y">';
        form_content += `<section>
            <div class="field">
                <div class="field">
                    <label class="field__label">Task Note</label>
                    <textarea class="w1/1" name="note" cols="24" rows="4"></textarea>
                </div>
                 <div class="field">
                    <label class="field__label">Groups</label>
                    <select multiple class="w1/1" name="groups[]">
                        ${options}
                    </select>
                </div>               
            </div>
        </section>`;
    }

    // Update Dynamic Form Elements
    $form.find('.form-contents').append(form_content);

    // Track Task Note
    $(document).on('keyup', '#' + $form.attr('id') + ' textarea[name="note"]', function() {
        task_note = $(this).val();
    });

    // Set up modal window
    $form.clone().dialog({
        width    : 600,
        dialogClass : 'task-action-dialog',
        position : {
            my: 'top',
            at: 'center',
            of: window,
            offset: '0 -200'
        },
        modal    : true,
        autoOpen : true,
        title    : title,
        open     : function () {
            const $this = $(this);
            $this.find('.process-button').on('click', function () {
                $.ajax({
                    'url'        : '/backend/inc/php/ajax/json.php?action=' + action,
                    'type'        : 'POST',
                    'dataType'    : 'json',
                    'data'        : $this.serialize(),
                    'success'  : function (json) {
                        if (json.success) {
                            var $success_form = $('<form action="?id=' + gid + '" method="post">'
                                + '<input type="hidden" name="user" value="' + task_uid + '">'
                                + '<input type="hidden" name="task" value="' + task_id + '">'
                                + '<input type="hidden" name="note" value="' + task_note + '">'
                                + '<input type="hidden" name="task_action" value="complete">'
                                + '</form>');
                            $('body').append($success_form);
                            $success_form.submit();
                        } else if (json.errors) {
                            showErrors(json.errors);
                        }
                    }
                });
                return false;
            });
            $this.find('.cancel-button').on('click', function (evt) {
                evt.preventDefault();
                $form.find('.form-contents').empty();
                $this.dialog('close');
                return;
            });
        },
        close: function () {
            $(this).dialog('destroy').remove();
            return false;
        }
    });
    $('select[data-selectize]').selectize({plugins: ['remove_button']});
    evt.preventDefault();
    return;
});

// Process Multiple Tasks by Type
$taskList.find('.process-task-group').click(function() {

    var $this = $(this),
        type = $this.attr('data-type'),
        tasks = {'user_task_ids' : $this.attr('data-ids').split(',')},
        count = tasks['user_task_ids'].length,
        message = '';

    // Build Instructional Message
    if (type === 'Email') {
        message = '<span>Are you sure you want to automatically send ' + count + ' ' + type.toLowerCase() + (count > 1 ? 's' : '') + '?</span>';
    } else if (type === 'Text') {
        message = '<span>Are you sure you want to automatically send ' + count + ' ' + type.toLowerCase() + (count > 1 ? 's' : '') + ' via REWText?</span>';
    } else if (type === 'Group') {
        message = '<span>Are you sure you want to automatically assign users to groups determined in ' + count + ' tasks?</span>';
    }

    var $mass_modal = $('<div id="mass-tasks-process">'
        + (message ? '<span>' + message + '</span>' : '')
        + '<div class="boxed">'
            + '<div class="field">'
                + '<div class="btns">'
                    + '<button type="submit" class="btn btn--positive process-tasks">Confirm</button>'
                    + '<button type="submit" class="btn btn--negative close-modal">Cancel</button>'
                + '</div>'
            + '</div>'
        + '</div>'
    + '</div>').dialog({modal:true});

    // Cancel Button
    $mass_modal.find('.close-modal').click(function() {
        $mass_modal.dialog('destroy').remove();
    });

    // Confirm Button
    $mass_modal.find('.process-tasks').click(function() {
        $.ajax({
            'url'        : '/backend/inc/modules/task-list/mass-action-ajax.php?action=process_tasks',
            'type'        : 'POST',
            'dataType'    : 'json',
            'data'        : tasks,
            'success'  : function (json) {
                var response_output = [],
                    $response_form;

                if (json.errors) {
                    $.each(json.errors, function() {
                        response_output.push('<input type="hidden" name="json_errors[]" value="' + $(this)[0] + '">');
                    });
                }
                if (json.success) {
                    $.each(json.success, function() {
                        response_output.push('<input type="hidden" name="json_success[]" value="' + $(this)[0] + '">');
                    });
                }
                $response_form = $('<form method="post">'
                    + '<input type="hidden" name="ajax_response" value="true">'
                    + response_output.join()
                + '</form>');

                $response_form.submit();
            }
        });
    });

    return false;
});

/**
 * Queue Call Tasks Into REWDialer
 */
$taskList.find('.queue-dialer-task-group').click(function () {
    var $this = $(this),
        tasks = {'user_task_ids' : $this.attr('data-ids').split(',')};

    $.ajax({
        'url'        : '/backend/inc/modules/task-list/mass-action-ajax.php?action=queue_dialer',
        'type'        : 'POST',
        'dataType'    : 'json',
        'data'        : tasks,
        'success'  : function (json) {
            var dialer_data = [],
                response_output = [];

            // Dialer Queue - User IDs
            if (json.dq) {
                dialer_data = json.dq;
            }

            if (dialer_data.length > 0) {
                const dialer_url = dialer_data.length === 1
                    ? URLS.backend + 'partners/espresso/interface/?leads=' + dialer_data[0] + '&popup'
                    : URLS.backend + 'partners/espresso/interface/?popup&leads=' + dialer_data.join(',');
                const $task_dialog = $('<div style="overflow: hidden;"></div>')
                    .html('<iframe style="border: 0px;" src="' + dialer_url + '" width="100%" height="100%"></iframe>')
                    .dialog({
                        autoOpen: false,
                        modal: true,
                        height: 650,
                        width: 1200,
                        resizable: false,
                        title: 'Processing Call Tasks',
                    });
                $task_dialog.dialog('open');
                // Refresh Page When Dialer is Closed to Refresh Task List
                $task_dialog.on('dialogclose', function() {
                    top.location.reload(true);
                });
            } else {
                json.errors = (json.errors) ? json.errors : [];
                json.errors.push('Failed to initiate REWDialer - No leads were provided.');
            }

            if (json.errors) {
                $.each(json.errors, function() {
                    response_output.push('<input type="hidden" name="json_errors[]" value="' + $(this)[0] + '">');
                });
                const $response_form = $('<form method="post">'
                    + '<input type="hidden" name="ajax_response" value="true">'
                    + response_output.join()
                + '</form>');
                $response_form.submit();
            }
        }
    });

    return false;
});

/**
 * Prevent background scrolling when task modal is opened
 * @param {Object} evt - The `Event` object
 * @returns null
 */
function toggleOpenClass(evt) {
    const rootElement = document.documentElement;
    const parent = document.getElementById('task-list');
    const modalOpened = parent.contains(evt.target) && evt.target.dataset.action === 'process';
    if (!rootElement.classList.contains('modal-open') && modalOpened) {
        rootElement.classList.add('modal-open');
    } else {
        if (evt.target.classList.contains('ui-dialog-titlebar-close')) rootElement.classList.remove('modal-open'); // jQueryUI dialog close button
    }
    if (evt.target.dataset.action === 'cancel') rootElement.classList.remove('modal-open');
}

document.addEventListener('click', (evt) => {
    toggleOpenClass(evt);
});

/**
 * Add utility class name to iframe HTML tag
 * Resolves scrolling issue on ios devices
 * BREW48-2135 - https://realestatewebmasters.atlassian.net/browse/BREW48-2135
 */
function iframeContentScroll() {
    const modalButtons = Array.from(document.querySelectorAll('[data-action=process]'));
    modalButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const iframe = document.querySelector('iframe');
            if (iframe) {
                iframe.onload = () => {
                    const iframeHTML = iframe.contentDocument.firstElementChild;
                    iframeHTML.classList.add('iframe-scroll');
                };
            }
        });
    });
}
iframeContentScroll();