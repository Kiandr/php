(function() {

    var currentDialog = '';
    var modals = {};
    var loading = {};
    var resultSuffix = '-result';

    function process (name, response) {

        var $modal = $(response);
        modals[name] = {
            'modal-header': $modal.find('.modal-header'),
            'modal-body': $modal.find('.modal-body')
        };
        if (!modals[name]['modal-header'].length) {
            // Failsafe in case this gets used on pages not prepared for modals
            modals[name]['modal-header'] = $modal.find('h1, h2').filter(function () {
                return !$(this).parents('.uk-modal').length;
            });
        }
        if (!modals[name]['modal-body'].length) {
            // Failsafe in case this gets used on pages not prepared for modals
            modals[name]['modal-body'] = $modal.find(':not(h1, h2)').filter(function () {
                var $this = $(this);
                return !$this.parents('.uk-modal').length && !$this.parents('#profile-report-controls').length;
            });
        }
        if (typeof(loading[name]) != 'undefined') {
            if (typeof(loading[name]) == 'function') {
                loading[name]();
            }
            delete loading[name];
        }
    }

    global.REW.LoadDialog = function (name) {
        var url = REW.settings.ajax.urls[name];
        if (typeof(url) == 'undefined') {
            throw 'Invalid modal requested.';
        }

        if (!loading[name]) {
            // This might be set to a callback already
            loading[name] = true;
        }

        if (url.indexOf('?popup=') == -1 && url.indexOf('&popup') == -1) {
            url += (url.indexOf('?') == -1 ? '?' : '&') + 'popup=true';
        }

        $.get(url, function (response) {
            process(name, response);
        });
    };

    global.REW.SetDialog = function (name, $el) {
        process(name, $el);
    };

    global.REW.Dialog = function (name, forceURL, allowBgClose) {
        if (forceURL) {
            if (!REW.settings.ajax.urls[name]) {
                // If this isn't really a valid dialog, make it so.
                REW.settings.ajax.urls[name] = forceURL;
            }

            name += resultSuffix;
            REW.settings.ajax.urls[name] = forceURL;
            loading[name] = function () {
                REW.Dialog(name, undefined, allowBgClose);
            };
            REW.LoadDialog(name);
        }

        if (typeof(allowBgClose) == 'undefined') {
            allowBgClose = true;
        }

        if (loading[name] || !modals[name]) {

            // Open dialog after load completes
            loading[name] = function () {
                loading[name] = false;
                REW.Dialog(name, undefined, allowBgClose);
            };

            // Preload dialog if it is unloaded
            if (!modals[name]) {
                REW.LoadDialog(name, undefined, allowBgClose);
            }
            return;
        }

        // Hide off canvas bar if it is displayed
        UIkit.offcanvas.hide();

        // Render template
        var $modal = $('.uk-modal.main-modal');
        var $container = $modal.find('.container');
        currentDialog = name.replace(resultSuffix, '');

        if (REW.settings.dialogs[name] && REW.settings.dialogs[name].large) {
            $modal.find('.uk-modal-dialog').addClass('uk-modal-dialog-large');
        } else {
            $modal.find('.uk-modal-dialog').removeClass('uk-modal-dialog-large');
        }

        $container.empty();
        for (var field in modals[name]) {
            var $replacementField = $(modals[name][field]);
            REW.Bind($replacementField);
            $replacementField.addClass('uk-' + field);
            $container.append($replacementField);
        }


        // Get modal object
        var modal = UIkit.modal($modal);

        if (!$container.find('form').length) {
            // If there are no forms, auto-refresh after 2.5 seconds

            if ($.inArray(currentDialog, REW.settings.ajax.refresh_after) > -1 && !$container.find('.uk-alert-danger').length) {
                setTimeout(function () {
                    window.location.reload();
                }, 2500);
            }
        }
        REW.activateTarget('view-grid', $modal);

        if (!modal.isActive() || REW.Dialog.allowBgClose != allowBgClose) {
            // Allow/disallow closing by clicking the background
            modal.options.bgclose = REW.Dialog.allowBgClose = allowBgClose;

            // Display dialog
            modal.show();
        } 
    };

    global.REW.DialogLinks = function () {
        // Nothing special about an auto dialog except that we'll always use the same URL as non-modal (+?popup of course)
        var autoIndex = 0;
        $('[data-modal-auto]').each(function () {
            var $this = $(this);
            var href = $this.attr('href');

            // Unusable dialog
            if (!href || href.indexOf('javascript:') == 0 || href.indexOf('mailto:') == 0) return;

            // Indicates that it's a popup
            href += (href.indexOf('?') > -1 ? '&' : '?') + 'popup';

            autoIndex++;
            var autoName = $this.data('modal-auto') || 'auto-' + String(autoIndex);

            $this.data('modal', autoName);

            // Add a class because jQuery won't actually add this data to the DOM element so our queries below won't find it.
            $this.addClass('auto-modal');
            REW.settings.ajax.urls[autoName] = href;
        });

        // Bind click events
        $(document).on('click', '[data-modal-reset]', function (event) {
            event.preventDefault();

            global.REW.Dialog(currentDialog);
        }).on('click', '[data-modal], .auto-modal', function (event) {
            event.preventDefault();

            global.REW.Dialog($(this).data('modal'));
            // @TODO: merge the following 2 functions
        }).on('submit', '.uk-modal.main-modal form', function (event) {
            event.preventDefault();

            var $this = $(this);
            if ($this.data('confirm')) {
                return;
            }

            REW.GoToURL($this.attr('action'), $this.attr('method') || 'GET', $this.serialize());
        }).on('click', '.uk-modal.main-modal a[href]:not(.oauth)', function (event) {
            var $this = $(this);
            var href = $this.attr('href');
            if ($this.attr('target') == '_blank' || $this.attr('target') == '_parent') {
                if ($this.attr('target') == '_parent') {
                    window.location = href;
                }
                // Don't process if target is a new window
                return true;
            }
            if (!href || href.indexOf('javascript:') == 0 || href.indexOf('mailto:') == 0) return;

            event.preventDefault();
            REW.GoToURL($this.attr('href'), 'GET');
        });
    };

    global.REW.GoToURL = function (url, method, data) {
        var $modal = $('.uk-modal.main-modal');
        var modal = UIkit.modal($modal);
        if (modal.isActive()) {
            url = url ? url : '';
            if (url.substr(0, 1) != '/' && url.substr(0, 4) != 'http') {
                if (REW.settings.ajax.urls[currentDialog].indexOf('?') > -1 && url.indexOf('?') == 0) {
                    url = '&' + url.substring(1);
                }

                url = REW.settings.ajax.urls[currentDialog] + url;
            }

            // Make sure to load the popup version
            url += (url.indexOf('?') == -1 ? '?' : '&') + 'popup=true';

            $.ajax(url, {
                method: method || 'GET',
                data: data,
                success: function (response) {
                    process(currentDialog + resultSuffix, response);
                    REW.Dialog(currentDialog + resultSuffix, undefined, REW.Dialog.allowBgClose);
                    $('.uk-modal.main-modal').scrollTop(0);
                }
            });
        } else {
            window.location.href = url;
        }
    };
})();
