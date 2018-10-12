import 'jquery-ui-timepicker-addon';
import URLS from 'constants/urls';
import showErrors from 'utils/showErrors';
import labelPicker from 'common/labelPicker';
import tinyMCE from 'tinymce';
import groupPicker from 'common/groupPicker';

// Enable AP's label style picker
labelPicker('select[name="style"]');

var $planBuilder = $('#plan-builder'),
    actionplanID = $('#actionplan-id').val(),
    $tasks = $('#all-tasks'),
    $typeMenu = $('.task-type-menu').menu().clone(),
    notify;

// Show task type select menu
$planBuilder.on('click', '.select-type', function () {

    if ($typeMenu.is(':visible')) {
        $typeMenu.hide();
        return false;
    }

    $typeMenu.insertAfter($(this));

    var $button = $(this);
    var task_id = $button.attr('data-task');
    if (typeof task_id == 'undefined') task_id = '';

    // Set task id on menu items
    $typeMenu.find('.task-action').each(function () {
        $(this).attr('data-task', task_id);
    });

    // Position & Show Menu
    $typeMenu.show().position({
        my: 'left top',
        at: 'left bottom',
        offset: '0 -4',
        of: $button
    });

    return false;

});

// Expand all subtasks on builder
$planBuilder.find('.expand-all').click(function () {
    $tasks.find('tr.task-subtasks').show();
    $tasks.find('.toggle-subtasks').addClass('open').children('i').removeClass('icon-plus-sign').addClass('icon-minus-sign');
    return false;
});

// Collapse all subtasks on builder
$planBuilder.find('.collapse-all').click(function () {
    $tasks.find('tr.task-subtasks').hide();
    $tasks.find('.toggle-subtasks').removeClass('open').children('i').removeClass('icon-minus-sign').addClass('icon-plus-sign');
    return false;
});

// Expand/collapse subtasks on a specific task
$planBuilder.on('click', '.toggle-subtasks', function () {
    var $toggle = $(this);
    var task_id = $toggle.data('task');
    if ($toggle.hasClass('open')) {
        $toggle.removeClass('open');
        $toggle.children('i').removeClass('icon-minus-sign').addClass('icon-plus-sign');
        $tasks.find('tr.task-subtasks[data-task="' + task_id + '"]').hide();
    } else {
        $toggle.addClass('open');
        $toggle.children('i').removeClass('icon-plus-sign').addClass('icon-minus-sign');
        $tasks.find('tr.task-subtasks[data-task="' + task_id + '"]').show();
    }
});

// Hide task type selector menu on document click
$(document).click(function (evt) {
    $typeMenu.hide();
    toggleOpenClass(evt);
});

/**
 * Prevent background scrolling when task modal is opened
 * @param {Object} evt - The `Event` object
 * @returns null
 */
function toggleOpenClass(evt) {
    const rootElement = document.documentElement;
    const parent = document.getElementById('plan-builder');
    const modalOpened = parent.contains(evt.target) && evt.target.dataset.action === 'edit';
    if (!rootElement.classList.contains('modal-open') && modalOpened) {
        rootElement.classList.add('modal-open');
    } else {
        if (evt.target.classList.contains('ui-dialog-titlebar-close')) rootElement.classList.remove('modal-open'); // jQueryUI dialog close button
    }
    if (evt.target.dataset.action === 'submit') rootElement.classList.remove('modal-open');
}

// Action Plan Start/End Date
$('input.datepicker').datepicker({
    showButtonPanel: true,
    closeText: 'Clear',
    onClose: function () {
        if ($(window.event.srcElement).hasClass('ui-datepicker-close')) {
            $(this).val('');
        }
    },
    changeMonth: true,
    changeYear: true,
    dateFormat: 'D, M. d, yy',
    minDate: 0
});

// Hide/show task actions on mouse enter/leave
$planBuilder.on({
    mouseenter : function () {
        $(this).find('.actions').show();
    }, mouseleave : function () {
        $(this).find('.actions').hide();
        // If Displayed, Hide Type Selection Menu
        $typeMenu.hide();
    }
}, '.task-content');

// Handle task actions (add/edit/delete)
$planBuilder.on('click', '.task-action', function (event) {
    event.preventDefault();

    var $button = $(this);

    var taskID = $button.attr('data-task');
    var taskAction = $button.attr('data-action');
    var taskType = $button.attr('data-type');

    // Hide Type Selection Menu
    $typeMenu.hide();

    if (taskAction == 'add') {

        // Load add task form (modal)
        loadTaskForm('add', {'actionplan_id' : actionplanID, 'task_id' : taskID, 'task_type' : taskType});

    } else if (taskAction == 'edit') {

        // Load edit task form (modal)
        loadTaskForm('edit', {'actionplan_id' : actionplanID, 'task_id' : taskID});

    } else if (taskAction == 'delete') {

        if (confirm('Are you sure you want to delete this task?')) {
            // Remove a task from the action plan
            $.ajax({
                url      : '?ajax&deleteTask',
                type     : 'POST',
                dataType : 'json',
                data     : {
                    'task_id' : taskID,
                    'actionplan_id' : actionplanID
                },
                success : function(json) {
                    if (json.success) {
                        drawPlan();
                    }
                    if (json.errors) {
                        /* Error Notifications */
                        if (notify) notify.close();
                        notify = $('#notifications').notify('create', 'notify-error', {
                            title : 'An Error Has Occurred!',
                            text: '<ul><li>' +  json.errors.join('</li><li>') + '</li></ul>'
                        });
                    }
                }
            });

        } else {
            return false;
        }

    }

});

// Redrawing plan after making changes
function drawPlan () {
    $.ajax({
        url      : '?ajax&drawPlanBuilder',
        type     : 'POST',
        dataType : 'json',
        data     : { 'actionplan_id' : actionplanID },
        success  : function(json) {
            if (json.plan_html) {
                $tasks.html(json.plan_html);
            }
            if (json.errors) {
                /* Error Notifications */
                if (notify) notify.close();
                notify = $('#notifications').notify('create', 'notify-error', {
                    title : 'An Error Has Occurred!',
                    text: '<ul><li>' +  json.errors.join('</li><li>') + '</li></ul>'
                });
            }
        }
    });
}

// Loads appropriate task form (edit or add) in a modal popup
// If adding, taskID is the parent id if applicable
// If editing, taskID is the id of the task to edit
function loadTaskForm (mode, options) {

    var actionplanID = options.actionplan_id;
    var taskID       = options.task_id;
    var taskType     = options.task_type;

    // AJAX Request for task form
    var request_url = '?ajax&loadTaskForm&actionplan_id=' + actionplanID;

    if (mode == 'add') {
        request_url += '&task_mode=add&task_type=' + taskType;
        if (typeof taskType == 'undefined') {
            alert('Error! Task type not defined!');
            return false;
        }
        if (typeof taskID !== 'undefined') {
            request_url += '&task_id=' + taskID;
        }
    } else if (mode == 'edit' && typeof taskID !== 'undefined') {
        request_url += '&task_mode=edit&task_id=' + taskID;
    } else {
        alert('Error loading task form, invalid mode!');
        return false;
    }

    // Initialize task form dialog with loading text
    var $task_form = $('<div><p>Loading Task...</p></div>').dialog({
        width       : ($(window).width() * 0.6),
        dialogClass : 'task-dialog',
        position    : {my : 'top', at : 'center', of : window, offset: '0 -400'},
        modal       : true,
        autoOpen    : true,
        resizable   : false,
        title       : 'Loading...',
        close : function () {
            // Destroy and remove on close
            $(this).dialog('destroy').remove();
        }
    });

    // Load appropriate (populated) task form via AJAX
    $.ajax({
        url      : request_url,
        type     : 'GET',
        dataType : 'json',
        success  : function(json) {
            // Task Form markup from JSON response
            $task_form.html(json.form_html);
            // Set dialog title
            var modalTitle = $task_form.find('.modal-title').text();
            $task_form.dialog('option', 'title', modalTitle);
            // Bind task form events
            bindTaskForm($task_form);
            groupPicker('select[name="groups[]"]');
        }
    });
}

// Bind events to task form elements
function bindTaskForm ($form) {

    // UI Buttons
    $form.find('.cancel-button').on('click', function () {
        $form.dialog('close');
        document.documentElement.classList.remove('modal-open');
        return false;
    });

    $(document).find('.ui-dialog-titlebar-close').on('touchstart', function () {
        $form.dialog('close');
        return false;
    });

    // Initialize TinyMCE for task info and email task body text areas
    $form.find('.info, .body').setupTinyMCE();

    // Time picker
    $form.find('.timepicker').timepicker({
        ampm: true,
        timeFormat: 'hh:mm TT',
        stepMinute: 15
    });

    // Initialize selectize plugin for any available inputs
    $form.find('select[data-selectize]').selectize({plugins: ['remove_button']});

    // Add/Edit Task
    $form.find('.task-submit').bind('click', function (event) {
        event.preventDefault();

        var $this = $(this),
            $form = $this.parents('form.task-form'),
            $modal = $this.parents('.ui-dialog-content');

        tinyMCE.triggerSave();

        $.ajax({
            url      : '?ajax&submitTaskForm',
            type     : 'POST',
            dataType : 'json',
            data     : $form.serialize(),
            success  : function(json) {
                if (json.success) {
                    // Redraw the plan
                    drawPlan();
                    // Close Modal
                    $modal.dialog('close');
                }
                if (json.errors) {
                    /* Error Notifications */
                    if (notify) notify.close();
                    notify = $('#notifications').notify('create', 'notify-error', {
                        title : 'An Error Has Occurred!',
                        text: '<ul><li>' +  json.errors.join('</li><li>') + '</li></ul>'
                    });
                }
            }
        });

    });
}

// Draw builder on initial load
drawPlan();

// Choose form letter document and load into editor
$(document).on('change', '#doc_id', function () {
    const $message = $('[data-target="email_message"]');
    const value = $(this).val();
    const editor = $message.tinymce();
    const message = editor ? editor.getContent() : $message.val();
    const confirmChange = message.length !== 0;
    let loadDoc = !confirmChange;
    if (confirmChange && value != '') {
        loadDoc = confirm(
            'Changing Pre-Built Emails will erase any changes to your message\n.'
            + 'Do you wish to continue?'
        );
    }
    if (loadDoc && value != '') {
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
                    $message.removeClass('off').setupTinyMCE();
                    $message.tinymce().setContent(doc);
                } else if (data.message) {
                    showErrors([data.message]);

                }
            }
        });
    }
});