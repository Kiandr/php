var radio_ad_images = window.radio_ad_images || 0;
if ($('#pod-form').length) {

    // Audio Uploader
    $('.audio-uploader').rew_uploader({
        upload : 'audio',
        extraParams : {
            type : 'landing_radio_audio'
        },
        // Update New Upload's Layout
        onUpload : function (e, f) {
            var id = f.id,
                file = f.file,
                $li = $('.audio-uploader').find('input[name="uploads[]"][value="' + id + '"]').closest('li');
                

            var image;
            if (radio_ad_images) {
                image = '<div class="title-wrap">\
                    <label>Image</label>\
                    <div class="uploader"></div>\
                </div>';
            }

            var $item = $([
                '<div class="wrap">',
                '<img style="display: none;">',
                '<div class="file-wrap">',
                '<label>File</label>',
                '<span>' + file + '</span>',
                '</div>',
                '<input type="hidden" name="uploads[]" value="' + id + '">',
                '<div class="title-wrap">',
                '<label>Title</label>',
                '<input type="text" name="ad-player[audio-files][' + id + '][title]" value="" placeholder="' + file.replace(/\.[a-zA-Z0-9]+$/, '').replace(/[_-]+/, ' ') + '">',
                '</div>',
                image,
                '</div>'
            ].join('\n'));

            $li.addClass('audio-li').html($item);

            if (radio_ad_images) {
                $item.find('.uploader').rew_uploader({
                    extraParams : {
                        type : 'landing_radio_image'
                    },
                    inputName : 'ad-player[audio-files][' + id + '][image]',
                    placeholder: 'Upload photo',
                    multiple : false
                });
            }

        }
    });

    // "As Heard On" Image Uploader
    $('#aho-uploader').rew_uploader({
        placeholder: 'Upload photo',
        extraParams : {
            type : 'landing_radio_aho'
        }
    });

    // Pod Image Uploaders
    $('.uploader').each(function () {
        var $this = $(this);
        $this.rew_uploader({
            extraParams : {
                type : 'landing_radio_image'
            },
            inputName : $this.attr('id'),
            placeholder: 'Upload photo',
            multiple : false
        });
    });

    // Bind Event: Toggle Edit Window
    $('body').on({
        'click' : function (e) {
            e.preventDefault();

            var $this = $(this),
                $id = $this.closest('.pod').attr('id'),
                pod = $('#edit-' + $id);

            // Display Selected Pod
            pod.toggleClass('hidden');
        }
    }, '.pods-sortable .pod a.edit-pod');

    // Bind Event: Delete Custom Pod
    $('body').on({
        'click' : function (e) {
            e.preventDefault();

            var $id = $(this).closest('.pod').attr('id');

            if ($('#' + $id).length > 0 && $('#edit-' + $id).length > 0) {
                $('#' + $id).remove();
                $('#edit-' + $id).remove();
                $('#pod-form').prepend('<input type="hidden" name="delete[]" value="' + $id + '">');
            }
        }
    }, '.pods-sortable .pod a.delete-pod');

    // Bind Event: Toggle Pods With Click
    $('body').on({
        'click' : function (e) {
            e.preventDefault();

            var $this = $(this),
                $this_pod = $this.closest('.pod'),
                $this_status = $this_pod.find('.pod-status');

            // Enable / Disable Pod
            if ($this.closest('.pods-sortable').prop('id') == 'pods-inactive') {
                $('#pods-active').append($this_pod);
                $this_pod.removeClass('hide-controls');
                $this_status.prop('name', 'pod[active][]');
                $this.prop('title', 'Disable Pod').html('<svg class="icon icon-minus"><use xlink:href="/backend/img/icos.svg#icon-minus"/></svg>');
            } else {
                $('#pods-inactive').append($this_pod);
                $this_pod.addClass('hide-controls');
                $this_status.prop('name', 'pod[inactive][]');
                $this.prop('title', 'Enable Pod').html('<svg class="icon icon-add"><use xlink:href="/backend/img/icos.svg#icon-add"/></svg>');
            }

            // Close All Pods on Click
            $('.p-content').addClass('hidden');
        }
    }, '.pods-sortable .pod a.switch-pod');

    // Add Tabs to Tabbed Content Pods
    $('.add-tab').find('a').on('click', function (e) {
        e.preventDefault();

        var $this = $(this),
            $add_tab = $this.closest('.add-tab'),
            $tabs_section = $this.closest('div.tabs-section'),
            $tabs = $tabs_section.find('.tabs'),
            num_tabs = $tabs_section.find('.control').length,
            $pod_name = $this.closest('.pod').prop('id'),
            $field_name = $this.prop('id'),
            bt_output;

        // Don't Exceed 12 Tabs
        if (num_tabs < 20) {

            // Build Tab Output
            bt_output = $([
                '<div class="control">',
                '<span class="title"></span>',
                '<a class="remove"><span class="ico"></span></a>',
                '</div>'
            ].join('\n'));

            // Dislay Output
            bt_output.insertBefore($add_tab);

            // Build Tab Fields Output
            $tabs.append([
                '<div class="tab' + ((num_tabs > 0) ? ' hidden' : '') + '">',
                '<label>Title</label>',
                '<input class="w1/1 tab-title" type="text" name="' + $pod_name + '[' + $field_name + '][' + Date.now() + '][title]" value="">',
                '<label>Content</label>',
                '<textarea class="tmce-new" name="' + $pod_name + '[' + $field_name + '][' + Date.now() + '][content]"></textarea>',
                '</div>'
            ].join('\n'));

            // TinyMCE Content Area
            $('.tmce-new').setupTinyMCE();
            $('.tmce-new').removeClass('tmce-new');

            // Add Bind Event to the New Buttons
            update_tab_events();

        }

        // Hide Add Button If Maximum Tabs Exist
        if (num_tabs >= 19) {
            $add_tab.addClass('hidden');
        }
    });

    // Tabbed Content Tab Events
    var update_tab_events = function () {

        // Setup/Reset Tab Edit Click Events
        $('.tabs-section .controls .control .title').off('click').on('click', function () {
            var $this = $(this),
                tab_index = $this.closest('.control').index(),
                $tabs_section = $this.closest('div.tabs-section'),
                $controls = $this.closest('div.tabs-section .controls');

            // Hide Input Sections
            $tabs_section.find('.tab').addClass('hidden');

            // Switch Current Tab
            $controls.find('.control').removeClass('current');
            $this.closest('.control').addClass('current');

            // Display Selected Input Selections
            $tabs_section.find('.tab').each(function () {
                if (tab_index === $(this).index()) {
                    $(this).removeClass('hidden');
                }
            });
        });


        // Setup/Reset Tab Remove Click Events
        var $removeBtn = $('.tabs-section .controls .control .remove');
        $removeBtn.off('click').on('click', function () {
            var $this = $(this),
                $control = $this.closest('.control'),
                $add_tab = $this.closest('.controls').find('.add-tab'),
                tab_index = $control.index(),
                $tabs_section = $this.closest('div.tabs-section');

            // Delete Tab Button
            $control.remove();

            // Display "Add Tab" Button
            $add_tab.removeClass('hidden');

            // Delete Tab Contents
            $tabs_section.find('.tab').each(function () {
                if (tab_index === $(this).index()) {
                    // Focus on First Tab if Selected Tab is Deleted
                    if ($control.hasClass('current')) {
                        $tabs_section.find('.controls .control .title:first').trigger('click');
                    }
                    $(this).remove();
                    return false;
                }
            });
        });

        $('.tabs-section .tabs .tab .tab-title').on('keyup', function () {
            var $this = $(this),
                tab_index = $this.closest('.tab').index(),
                $tabs_section = $this.closest('div.tabs-section');

            $tabs_section.find('.control').each(function () {
                if (tab_index === $(this).index()) {
                    var new_title = $this.val();

                    new_title = (new_title.length > 5) ? new_title.trim().substring(0, 5) + '...' : new_title;

                    $(this).find('.title').html(new_title);
                    return false;
                }
            });
        });

    };
    update_tab_events();

    // Bind Event: Add a Custom Pod
    $('#add-custom-pod').on('click', function (e) {
        e.preventDefault();

        const pod_name = 'custom-' + $.now();

        $('.p-content').addClass('hidden');

        // Append the New Pod to the Active Pod Section
        $('#pods-active').append([
            '<li id="' + pod_name + '" class="node pod custom">',
            '<div class="article">',
            '<input type="hidden" class="pod-status" name="pod[active][]" value="' + pod_name + '">',
            '<span class="ttl p-header p-title">Custom</span>',
            '<div class="btns R p-controls">',
            '<a class="btn btn--ghost negative delete-pod" href="#" title="Delete Pod">',
            '<svg class="icon icon-trash mar0"><use xlink:href="/backend/img/icos.svg#icon-trash"/></svg>',
            '</a>',
            '<a class="btn btn--ghost edit-pod" href="#" title="Edit Pod">',
            'Edit',
            '</a>',
            '<a class="btn btn--ghost switch-pod" href="#" title="Disable Pod">',
            '<svg class="icon icon-minus"><use xlink:href="/backend/img/icos.svg#icon-minus"/></svg>',
            '</a>',
            '<div id="pod-form-append-' + pod_name + '" class="hidden"></div>',
            '</div>',
            '<div id="edit-' + pod_name + '" class="p-content hidden">',
            '<fieldset class="pod-field">',
            '<label>Content</label>',
            '<textarea class="tmce" name="' + pod_name + '[content]"></textarea>',
            '</fieldset>',
            '</div>',
            '</li>'
        ].join('\n'));

        $('#' + pod_name).find('textarea.tmce').setupTinyMCE();
    });

    // Pre-Click Event for Pod Sorting
    var oldMouseStart = $.ui.sortable.prototype._mouseStart;
    $.ui.sortable.prototype._mouseStart = function(event, overrideHandle, noActivation) {
        this._trigger('CustomBeforeStart', event, this._uiHash());
        oldMouseStart.apply(this, [event, overrideHandle, noActivation]);
    };

    // Make Pods Sortable
    $('#pods-active, #pods-inactive').sortable({
        handle				: '.p-header',
        connectWith			: '.pods-sortable',
        placeholder			: 'pods-sortable-ph',
        cursorAt			: {
            left : 250,
            top : 20
        },
        'CustomBeforeStart'	: function() {
            // Close Pod Edit Sections Before Sorting
            $('.p-content').addClass('hidden');
        },
        start				: function(event, ui) {
            ui.item.css({
                width : $('#pods-inactive').css('width')
            });

            // Highlight Draggable Areas
            $('.pods-sortable').addClass('live');

            // Clear TinyMCE Editors - They Don't React Well to Being Sorted Around
            ui.item.find('textarea.tmce').each(function() {
                tinymce.editors = []; //eslint-disable-line no-undef
            });
        },
        over				: function(event, ui) {
            // Toggle Pod Controls and Status
            if ($(this).is('#pods-active')) {
                ui.item.removeClass('hide-controls');
                ui.item.find('.pod-status').prop('name', 'pod[active][]');
                ui.item.find('.switch-pod').prop('title', 'Disable Pod').html('<svg class="icon icon-minus"><use xlink:href="/backend/img/icos.svg#icon-minus"/></svg>');
            } else {
                ui.item.addClass('hide-controls');
                ui.item.find('.pod-status').prop('name', 'pod[inactive][]');
                ui.item.find('.switch-pod').prop('title', 'Enable Pod').html('<svg class="icon icon-add"><use xlink:href="/backend/img/icos.svg#icon-add"/></svg>');
            }
        },
        stop				: function(event, ui) {
            const $a_pods = $('#pods-active').find('.pod');
            const $ia_pods = $('#pods-inactive').find('.pod');

            // Remove Highlight
            $('.pods-sortable').removeClass('live');

            // Toggle active pod section hint
            if ($a_pods.length) {
                $('#pods-active').find('.pod-hint').hide();
            } else {
                $('#pods-active').find('.pod-hint').show();
            }

            // Toggle inactive pod section hint
            if ($ia_pods.length) {
                $('#pods-inactive').find('.pod-hint').hide();
            } else {
                $('#pods-inactive').find('.pod-hint').show();
            }

            // Set TinyMCE Back Up For Pods Once Sorting is Completed
            ui.item.find('textarea.tmce').each(function() {
                $(this).setupTinyMCE();
            });
        }
    });

    $('#pods-active').find('textarea.tmce').setupTinyMCE();

}