import URLS from 'constants/urls';

/**
 * $.ui.rew_manager
 *
 * $('#manage-event-types').rew_manager({
 *     type: 'eventType',
 *     title: 'Manage Event Types',
 *     optionText: 'Type',
 *     options: $('#select-event-types')
 * });
 *
 */
$.widget('ui.rew_manager', {

    options: {
        type        : null, // type of manager, used in AJAX:(eventType, listingType, listingStatus, listingFeature)
        title       : 'Manage Options',
        optionText  : 'Option',
        options     : null, // array, url, or <select>
        extraParams : {}, // for $.ajax
        onAdd       : null,
        onRemove    : null
    },

    _create: function() {

        var self = this,
            o = self.options,
            el = self.element,
            html = '\
                <div class="rewui rewmodule">\
                    <div class="rewui message highlight hidden"></div>\
                    <div class="field">\
                        <label class="field__label">Add New ' + o.optionText + '</label>\
                        <input class="w1/1" type="text" name="option" value="">\
                    </div>\
                    <button type="button" class="btn option-add">Add New ' + o.optionText + '</button>\
                    <div class="field">\
                        <label class="field__label">Remove ' + o.optionText + '</label>\
                        <select class="w1/1" name="options"></select>\
                    </div>\
                    <button type="button" class="btn btn--negative option-remove">Remove ' + o.optionText + '</button>\
                </div>';

        // modal window
        this.modal = $(html).dialog({
            modal    : true,
            autoOpen : false,
            title    : o.title
        });

        // open manager
        el.bind('click', function () { self._showModal(); });

        // add option
        this.modal.find('.option-add').bind('click', function () {
            self._addOption();
        });

        // add option (on enter)
        this.modal.find('input[name="option"]').bind('keypress', function (e) {
            if (e.which == 13) self._addOption();
        });

        // remove option
        this.modal.find('.option-remove').bind('click', function () {
            self._removeOption();
        });

        // load options
        self._loadOptions();

    },

    refresh: function () {
        this._loadOptions();
    },

    _hideModal: function () {
        this._hideMessage();
        this.modal.dialog('close');
    },

    _showModal: function () {
        this._hideMessage();
        this.modal.dialog('open');
    },

    _showMessage : function (message) {
        this.modal.find('.message').html(message).removeClass('hidden');
    },

    _hideMessage : function () {
        this.modal.find('.message').addClass('hidden').html('');
    },

    _addOption: function () {
        var self = this,
            modal  = self.modal,
            o = self.options,
            select = o.select,
            options = o.options,
            input = modal.find('input[name="option"]'),
            option = input.val();

        // require option
        if (option.length == 0) return;

        // Reset Message
        self._hideMessage();

        // AJAX request to save option
        $.ajax({
            'url'      : URLS.backendAjax + 'json.php?addOption',
            'type'     : 'POST',
            'dataType' : 'json',
            'data' : {
                type   : o.type,
                params : o.extraParams,
                option : option
            },
            'error' : function () {
                self._showMessage('<p>Error Occurred, Please contact support.</p>');
            },
            'success' : function (data) {

                // Error Occurred
                if (data.errors && data.errors.length > 0) {
                    self._showMessage('<p>' + data.errors.join('<br>') + '</p>');

                } else {

                    // require new option
                    var option = data.option;
                    if (option) {

                        // add new option
                        options.push(option);

                        // update options
                        self._updateOptions();

                        // reset option
                        input.val('').trigger('blur');

                        // update <select>
                        if (select) {
                            select.append('<option value="' + self._htmlspecialchars(option.value) + '" selected="selected">' + option.title + '</option>');
                        }

                        // trigger callback
                        self._trigger('onAdd', null, option);

                        // hide modal
                        self._hideModal();

                    }

                }
            }
        });

    },

    _removeOption: function () {
        var self = this,
            modal  = self.modal,
            o = self.options,
            select = o.select,
            options = o.options,
            option = modal.find('select[name="options"]').val();
        if (option.length == 0) return;
        if (confirm('Are you sure you want to more the selection option?')) {

            // find and remove option
            var count = options.length;
            if (count > 0) {
                var i = 0;
                while (i < count) {
                    if (options[i].value == option) {
                        option = options.splice(i, 1).shift();
                        count--;
                        break;
                    }
                    i++;
                }
            }

            // AJAX request to save option
            $.ajax({
                'url'      : URLS.backendAjax + 'json.php?removeOption',
                'type'     : 'POST',
                'dataType' : 'json',
                'data' : {
                    type   : o.type,
                    params : o.extraParams,
                    option : option.value
                },
                'error' : function () {
                    self._showMessage('<p>Error Occurred, Please contact support.</p>');
                },
                'success' : function () {

                    // update options
                    self._updateOptions();

                    // update <select>
                    if (select) {
                        select.find('option[value="' + option.value + '"]').remove();
                        if (select.find('option[value!=""]').length <= 0) {
                            select.html('<option value="">No Options Available</option>');
                        }
                    }

                    // trigger callback
                    self._trigger('onRemove', null, option);

                }
            });

        }
    },

    _loadOptions: function () {
        var self = this,
            o = self.options,
            options = o.options;

        // load options Array
        if (options && $.isArray(options)) {

            // set options
            this.options.options = options;

            // update options
            this._updateOptions();

            return;
        }

        // load via AJAX
        if (options && typeof(options) == 'string') {

            // ajax request
            $.ajax({
                'url'      : options,
                'type'     : 'POST',
                'dataType' : 'json',
                'success'  : function (data) {
                    // set options
                    self.options.options = data.options;
                    // update options
                    self._updateOptions();
                }
            });

            return;
        }

        // load options from DOM <select>
        if (options && typeof(options) == 'object') {

            // save select (we will update it onAdd/onRemove
            o.select = options;

            // locate options
            var opts = options.find('option'), values = [], len = opts.length;
            if (len > 0) {
                var i = 0;
                while (i < len) {
                    var option = opts[i], required = $(option).data('required');
                    values.push({
                        value : option.value,
                        title : option.innerHTML,
                        required : required
                    });
                    i++;
                }
            }

            // set options
            this.options.options = values;

            // update options
            this._updateOptions();

            return;
        }

    },

    _updateOptions: function () {
        var self = this,
            o = self.options,
            select = this.modal.find('select[name="options"]'),
            options = o.options,
            html = [];
        var count = options ? options.length : 0;
        if (count > 0) {
            var i = 0, first = '<option value="">Choose One</option>';
            while (i < count) {
                var option = options[i];
                if (!option.required) {
                    if (option.value.length > 0) {
                        html.push('<option value="' + self._htmlspecialchars(option.value) + '">' + option.title + '</option>');
                    } else {
                        first = '<option value="">' + option.title + '</option>';
                    }
                }
                i++;
            }
        }
        if (html.length > 0) {
            select.html(first + html.join('')).removeAttr('disabled');
        } else {
            select.html('<option value="">No Options Available</option>').attr('disabled', 'disabled');
        }

    },

    _htmlspecialchars : function (string, quote_style, charset, double_encode) {
        // http://kevin.vanzonneveld.net

        var optTemp = 0, i = 0, noquotes= false;
        if (typeof quote_style === 'undefined' || quote_style === null) {
            quote_style = 2;
        }
        string = string.toString();
        if (double_encode !== false) { // Put this first to avoid double-encoding
            string = string.replace(/&/g, '&amp;');
        }
        string = string.replace(/</g, '&lt;').replace(/>/g, '&gt;');

        var OPTS = {
            'ENT_NOQUOTES': 0,
            'ENT_HTML_QUOTE_SINGLE' : 1,
            'ENT_HTML_QUOTE_DOUBLE' : 2,
            'ENT_COMPAT': 2,
            'ENT_QUOTES': 3,
            'ENT_IGNORE' : 4
        };
        if (quote_style === 0) {
            noquotes = true;
        }
        if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
            quote_style = [].concat(quote_style);
            for (i=0; i < quote_style.length; i++) {
                // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
                if (OPTS[quote_style[i]] === 0) {
                    noquotes = true;
                }
                else if (OPTS[quote_style[i]]) {
                    optTemp = optTemp | OPTS[quote_style[i]];
                }
            }
            quote_style = optTemp;
        }
        if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
            string = string.replace(/'/g, '&#039;');
        }
        if (!noquotes) {
            string = string.replace(/"/g, '&quot;');
        }

        return string;

    },

    destroy: function() {
        var self  = this,
            el    = self.element,
            modal = self.modal;

        // destroy widget
        $.Widget.prototype.destroy.apply(this, arguments);

        // remove modal
        modal.remove();

        // remove link
        el.remove();

    }

});