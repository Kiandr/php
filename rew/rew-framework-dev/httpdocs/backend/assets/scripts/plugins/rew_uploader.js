import qq from 'legacy/fileuploader';
import Sortable from 'sortablejs';

// Allowed file extensions for image uploads
const imgTypes = ['jpg', 'jpeg', 'png', 'gif'];

// Allowed file extensions for document uploads
const docTypes = ['doc', 'docx', 'rtf', 'pdf', 'txt', 'xls', 'xlsx', 'odt'];

// $.ui.rew_uploader
$.widget('ui.rew_uploader', {
    options: {
        upload: 'img', // img, doc, or audio
        inputName: 'uploads[]',
        inputClass: 'skip-check',
        url_upload: '/backend/inc/php/ajax/upload.php?upload', // upload script
        url_delete: '/backend/inc/php/ajax/upload.php?delete', // upload script
        url_sort: '/backend/inc/php/ajax/upload.php?sort', // upload script
        url_thumb: '/backend/inc/php/ajax/upload.php?thumb', // upload script
        placeholder: 'Upload a file',
        extraParams: {}, // option.upload automatically added
        fileTypes: [], // see _create
        required: false, // required upload (must be within <form>)
        multiple: true, // allow multiple files
        sortable: true, // allow sortable list
        buttons: {
            edit: false,
            view: false,
            'delete': true
        },
        onUpload: null, // callback: function (event, file)
        onDelete: null, // callback: function (event, file)
        onEdit: null, // callback: function (event, file)
        onView: null, // callback: function (event, file)
        onSort: null // callback: function (event, order)
    },
    _create: function() {
        var self = this,
            o = self.options,
            el = self.element,
            html = '<div class="file-manager"><ul></ul></div>';

        // Upload Images
        if (o.upload == 'img') {
            if (o.fileTypes.length == 0) o.fileTypes = imgTypes;
            o.extraParams.upload = o.upload;
        }

        // Upload Documents
        if (o.upload == 'doc') {
            if (o.fileTypes.length == 0) o.fileTypes = docTypes;
            o.extraParams.upload = o.upload;
        }

        // Upload Audio Files
        if (o.upload == 'audio') {
            if (o.fileTypes.length == 0) {
                o.fileTypes = ['mp3'];
            }
            o.extraParams.upload = o.upload;
        }

        /* File Uploader */
        self._setupUploader();

        /* File Manager */
        this.manager = el.find('.file-manager:first');
        if (this.manager.length == 0) {
            this.manager = $(html).appendTo(el).show();
        }

        // Error message container
        this.errorMsg = $('<div class="file-manager-error" />')
            .appendTo(el);

        /* Sortable */
        if (o.sortable) {
            self._setupSortable();
        }

        /* Actions */
        self._setupActions();

        if (o.required) {
            self._setupRequired();
        }

        // Disable widget
        if (o.disabled) {
            this.disable();
        }

    },

    _setupUploader: function() {

        var self = this,
            o = self.options,
            el = self.element,
            error = this.error.bind(this);

        /* File Uploader */
        this.uploader = new qq.FileUploader({
            element: el[0],
            action: o.url_upload,
            params: o.extraParams,
            multiple: o.multiple,
            messages: {
                placeholder: o.placeholder
            },
            showMessage: error,
            allowedExtensions: o.fileTypes,
            onSubmit: function(id, file) { // eslint-disable-line no-unused-vars
                error('');
                if (!o.multiple) {
                    if (self.uploader.getInProgress() != 0) return false;
                    var $items = self.manager.find('ul li');
                    if ($items.length >= 1) {
                        if (confirm('Are you sure you want to replace your existing upload?')) {
                            var upload = $items.find(':input').val();
                            if (self._trigger('onDelete', null, {
                                el: $items,
                                upload: upload
                            }) !== false) {
                                self._deleteUpload($items, upload);
                            }
                            return true;
                        }
                        return false;
                    }
                }
            },
            onUpload: function(id, fileName) { // eslint-disable-line no-unused-vars
                self._isLoading = true;
            },
            onCancel: function(id, fileName) { // eslint-disable-line no-unused-vars
                self._isLoading = false;
            },
            onComplete: function(id, file, json) {
                self._isLoading = false;
                if (json && json.success) {

                    self._addUpload({
                        id: json.id,
                        file: json.file,
                        name: json.name,
                        ext: json.ext
                    });

                    self._addThumb({
                        id: json.id,
                        file: json.file,
                        name: json.name,
                        ext: json.ext,
                    });

                    self._trigger('onUpload', null, json);

                }
            }
        });

    },

    _addUpload: function(upload) {

        // Upload preview
        var o = this.options, $img;
        if (o.extraParams.upload == 'img') {
            $img = $('<img />', {
                alt: '',
                src: '/thumbs/95x95' + upload.file + '?' + new Date().valueOf()
            });
        } else if (o.extraParams.upload == 'doc') {
            $img = $('<img />', {
                alt: '',
                src: '/img/icons/blank-doc.png'
            });
        } else {
            $img = $('<span class="file ico-' + upload.ext + '">' + upload.name + '</span>');
        }

        // Add hidden input to form
        var $input = $('<input />', {
            type: 'hidden',
            name: o.inputName,
            value: upload.id
        }).addClass(o.inputClass);

        // Save upload details
        $input.data('upload', {
            id: upload.id,
            file: upload.file,
            name: upload.name,
            ext: upload.ext
        });

        // Add upload to list
        var $list = this.manager.find('ul:first');
        if (o.multiple) {
            $img.appendTo($list).wrap('<li><div class="wrap"></div></li>');
        } else {
            $img.appendTo($list.html('')).wrap('<li><div class="wrap"></div></li>');
        }
        $input.insertAfter($img);

    },

    _addThumb : function (upload) {

        var self = this, o = self.options;
        self.error('');
        if (o.extraParams.upload == 'img') {
            $.ajax({
                url: o.url_thumb,
                type: 'POST',
                data: {
                    upload: upload
                }
            });
        }

    },

    _setupActions: function() {

        var self = this;
        var o = self.options;
        var $list = self.manager.find('ul:first');

        /* Action Buttons (Edit/View/Delete) */
        var actions = [];
        if (o.buttons['edit']) actions.push('<a href="#" class="btn edit">Edit</a>');
        if (o.buttons['view']) actions.push('<a href="#" class="btn view">View</a>');
        if (o.buttons['delete']) actions.push('<a href="#" class="btn delete">Delete</a>');

        /* Actions Container */
        var $actions = $('<div class="actions">' + actions.join('\n') + '</div>');
        var num_actions = $actions.length;

        /* Toggle Actions Display */
        $list.delegate('li', 'mouseenter', function(e) {

            e.stopPropagation();

            var $item = $(this),
                $wrap = $item.find('>.wrap'),
                $img = $wrap.children('img:first'),
                $input = $wrap.children('input:first');

            if (!$item.closest('ul').is($list)) return;

            /* Upload ID */
            const upload = $input.val();

            /* Insert Actions */
            $actions.insertAfter($img).show();
            $actions.addClass('num_actions_' + num_actions);

            /* Edit Action */
            $actions.find('.edit').bind('click', function() {
                if (self._trigger('onEdit', null, upload) === false) {
                    return;
                }
                return false;
            });

            /* View Action */
            $actions.find('.view').bind('click', function() {
                if (self._trigger('onView', null, upload) === false) {
                    return;
                }
                return false;
            });

            /* Delete Action */
            $actions.find('.delete').bind('click', function() {
                if (self._trigger('onDelete', null, {
                    el: $item,
                    upload: upload
                }) === false) {
                    return false;
                }
                if (confirm('Are you sure you want to delete this upload?')) {
                    self._deleteUpload($item, upload);
                }
                return false;
            });

        });

        $list.delegate('li', 'mouseleave', function() {
            $(this).find('.actions').remove();
        });

    },
    _deleteUpload: function($item, upload) {
        var self = this;
        var o = self.options;
        self.error('');
        $.ajax({
            url: o.url_delete,
            type: 'POST',
            data: {
                upload: upload
            },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    $item.fadeOut(function() {
                        $item.remove();
                    });
                } else if (data.error) {
                    self.error(data.error);
                }
            },
            error: function() {
                self.error('Error Occurred');
            }
        });
    },
    _setupSortable: function() {
        var self = this;
        var o = self.options;
        var $list = self.manager.find('ul:first');
        Sortable.create($list[0], {
            animation: 250,
            onUpdate : function() {
                var order = [];
                $list.find('input').each(function() {
                    order.push(this.value);
                });
                order = order.join(',');
                if (self._trigger('onSort', null, order) === false) {
                    return;
                }
                // Save
                $.ajax({
                    url: o.url_sort,
                    type: 'POST',
                    data: {
                        order: order
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            //
                        } else if (data.error) {
                            alert(data.error);
                        }
                    },
                    error: function() {
                        alert('Error Occurred');
                    }
                });
            }
        });

    },
    /**
     * This is a bit hacky - but is used require an upload on form submission
     */
    _setupRequired: function() {
        var self = this,
            element = self.element,
            $form = element.closest('form');
        if ($form.length === 0) return;

        // Bind submit.rew_uploader event to validate form submission
        $form.off('submit.rew_uploader').on('submit.rew_uploader', function() {
            var submit = true;
            $form.find(':ui-rew_uploader').each(function() {
                submit = $(this).rew_uploader('validate') || submit;
            });
            return submit;
        });

        // Run the validation function first...
        var events = $._data($form[0]).events;
        var registered = events.submit;
        registered.unshift(registered.pop());
        events.submit = registered;

    },
    /**
     * Check uploader requirements
     * @return {Boolean}
     */
    _checkRequired: function() {

        // Requirements met
        var uploads = this.getUploads();
        if (uploads.length > 0) return true;
        if (this.options.disabled) return true;
        if (!this.options.required) return true;

        // Requirements failed
        var placeholder = this.options.placeholder;
        var errorMessage = 'You must ' + placeholder.toLowerCase() + '.';
        this.error(errorMessage);
        return false;

    },
    /**
     * Validate uploader
     * @return {Boolean}
     */
    validate: function() {
        return this._checkRequired();
    },
    /**
     * Add upload to list
     * @param {Object} upload
     */
    addUpload: function(upload) {
        this._addUpload(upload);
    },
    /**
     * Get array of uploaded files
     * @return {Array}
     */
    getUploads: function() {
        var uploads = [];
        var inputName = this.options.inputName;
        var $inputs = this.manager.find(':input[type="hidden"][name="' + inputName + '"]');
        $inputs.each(function() {
            var $input = $(this);
            var upload = $input.data('upload');
            if (typeof upload === 'object') {
                uploads.push(upload);
            } else {
                uploads.push({
                    id: $input.val(),
                    file: null,
                    name: null,
                    ext: null
                });
            }
        });
        return uploads;
    },
    /**
     * Return current error message
     * @return {String}
     */
    getError: function() {
        return this.errorMsg.html();
    },
    /**
     * Check if currently loading
     * @return {Boolean}
     */
    isLoading: function() {
        return this._isLoading;
    },
    /**
     * Destroy uploader instance
     */
    destroy: function() {
        $.Widget.prototype.destroy.apply(this, arguments);
        //this.element.remove();
        this.element.html('');
    },
    /**
     * Reset uploader instance
     */
    reset: function() {
        this.destroy();
        var opts = this.options;
        this.element.rew_uploader(opts);
    },
    /**
     * Set error message
     * @param {String} message
     */
    error: function(message) {
        this.errorMsg.html(message);
    }
});
