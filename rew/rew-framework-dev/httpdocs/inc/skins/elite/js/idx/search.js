require('./mapping');

(function () {
    var refining = window.location.href && (window.location.href.indexOf('?refine=') > -1 || window.location.href.indexOf('&refine=') > -1);
    var firstSearch = true;
    var advancedSearchTarget = '.fw-idx-filter-container';
    var mirrorFields = REW.settings.qs_mirrored_fields;

    // Match Location/Price Input Values on Both Forms
    $.each(mirrorFields, function(index, field_name) {
        $(document).find('.js-idx-search-header').find('[name="' + field_name + '"]').on('keyup keypress blur change', function() {
            // Update the Advanced Form's Matching Field
            $(advancedSearchTarget).find('input[name="' + field_name + '"],select[name="' + field_name + '"]').val($(this).val());
        });
    });

    $('.js-idx-search-header, #map-draw-controls').on('checkMap', function () {

        // Disable if Polygon Set
        var $polygon = $('#map-draw-controls input[name="map[polygon]"]').val();
        if ($polygon && $polygon.length > 0) {
            return 'Polygon';
        }

        // Disable if Radius Set
        var $radius = $('#map-draw-controls input[name="map[radius]"]').val();
        if ($radius && $radius.length > 0) {
            return 'Radius';
        }

        // Map Has Active Polygon / Radius Searches
        if (typeof $map != 'undefined') {
            if ($map.REWMap('getPolygons')) return 'Polygon';
            if ($map.REWMap('getRadiuses')) return 'Radius';
        }

        // Disable if Bounds Checked
        if ($('#map-draw-controls input[name="map[bounds]"]').is(':checked')) return 'Bounds';

        // No Map Criteria
        return false;

    });

    if (!REW.Helpers.isMapSearch()) {
        /**
         * Monitor submission of the quicksearch form. If the advanced search has been opened already,
         * submit that one instead.
         */
        $('.js-idx-search-header').on('submit', function (event) {
            var $advancedSearch = $(advancedSearchTarget + ' form');
            if ($advancedSearch.length) {

                // Stop the header form from submitting. Submit the advanced search in place.
                event.preventDefault();
                $(advancedSearchTarget).find('form').trigger('submit');
            }
        });
    }

    //View toggle removes office and disclaimer when compliance rule met
    window.onload = function() {
        if(document.getElementById('gridViewOffice')) {

            var g = document.getElementById('gridViewOffice');
            var d = document.getElementById('detailedViewOffice');

			 if(window.location.href.indexOf('grid') > -1) {
				  $('.office').addClass('hidden');
			 }

            g.addEventListener('click', gridView, false);
            d.addEventListener('click', detailedView, false);

            function gridView() {
                $('.office').addClass('hidden');
            }

            function detailedView() {
                $('.office').removeClass('hidden');
            }
        }

        if(document.getElementById('gridView')) {

            var g = document.getElementById('gridView');
            var d = document.getElementById('detailedView');

			 if(window.location.href.indexOf('grid') > -1) {
				  $('.office').addClass('hidden');
				  $('.mls-disclaimer').addClass('hidden');
			 }

			 g.addEventListener('click', gridView, false);
			 d.addEventListener('click', detailedView, false);

            function gridView() {
                $('.office').addClass('hidden');
                $('.mls-disclaimer').addClass('hidden');
            }

            function detailedView() {
                $('.office').removeClass('hidden');
                $('.mls-disclaimer').removeClass('hidden');
            }
		  }
    }; 	
    
    /**
     * Flattens an object of any depth into proper form element names.
     *
     * @param obj
     * @returns object
     */
    function flattenObject(obj) {

        var result = {};
        var _shipIt = function (obj, prefix) {

            switch (typeof(obj)) {
            case 'object':
                for (var key in obj) {
                    _shipIt(obj[key], prefix ? prefix + '[' + (typeof(key) == 'number' ? '' : key) + ']' : key);
                }
                break;
            case 'string':
                result[prefix] = obj;
                break;
            }
        };

        _shipIt(obj);

        return result;
    }

    /**
     * If there is a criteria object, ensure all IDX forms (quick search, map controls) are replaced
     * with that data.
     * This is assumed to run before the advanced search is loaded. It will only work for select
     * lists and text inputs.
     */
    function loadFormsFromGlobalCriteria() {
        if (!window.criteria) return;

        var $forms = getForms(true);

        // First set everything empty
        $forms.find('input, select').not('[name^="map["]').not('[name="search_location"]').val('');

        var flatCriteria = flattenObject(window.criteria);
        for (var name in flatCriteria) {
            $forms.each(function () {
                var $this = $(this);
                var $currentElement = $this.find('input[name^="' + name + '"][type="hidden"], input[name^="' + name + '"][type="text"], select[name^="' + name + '"]');
                if ($currentElement.length) {
                    $currentElement.val(flatCriteria[name]);
                } else {

                    // This field isn't in the form. Add it (hidden)
                    $currentElement = $('<input type="hidden">');
                    $currentElement.attr('name', name);
                    $currentElement.attr('value', flatCriteria[name]);
                    $this.append($currentElement);
                }
            });
        }

        if (REW.SetPriceLabel) {
            // Re-generate price label in the header.
            REW.SetPriceLabel();
        }
    }
    if (window.criteria) {
        loadFormsFromGlobalCriteria();
    }

    function storeFormInGlobalCriteria($form, defaultCriteria) {
        window.criteria = {};

        if (defaultCriteria) {
            for (var name in defaultCriteria) {
                if (name.substring(name.length-2) == '[]') {
                    var n = name.replace('[]','');
                    if (!window.criteria[n]) {
                        window.criteria[n] = [];
                    }
                    window.criteria[n].push(defaultCriteria[n]);
                } else {
                    window.criteria[name] = defaultCriteria[name];
                }
            }
        }

        $.each($form.serializeArray(), function () {
            if (this.name.substring(this.name.length-2) == '[]') {
                var n = this.name.replace('[]','');
                if (!window.criteria[n]) {
                    window.criteria[n] = [];
                }
                window.criteria[n].push(this.value);
            } else {
                window.criteria[this.name] = this.value;
            }
        });
    }

    function removeDuplicatesFromQueryString(qs) {
        var uniqueStorage = [];
        var uniqueQueryString = [];
        var pairs = qs.split('&');
        var pairCount = pairs.length;
        for (var i = 0; i < pairCount; i++) {
            var pair = pairs[i];

            // Consider field and field[] to be the same
            var strippedPair = pair.replace('[]', '');
            if (strippedPair && uniqueStorage.indexOf(strippedPair) == -1) {
                uniqueQueryString.push(pair);
                uniqueStorage.push(strippedPair);
            }
        }

        return uniqueQueryString.join('&');
    }

    function getSearchArgumentsAndRewriteForms() {
        var $match = $(advancedSearchTarget);
        var qs, loadedSavedSearch = false;

        if (!REW.Helpers.isMapSearch() && typeof criteria != 'undefined') {
            loadedSavedSearch = (criteria ? criteria.hasOwnProperty('saved_search_id') : REW.qs.hasOwnProperty('saved_search_id'));
        }
        // Store everything in strings since there are multiple data types and in JS a query string
        // is the easiest to work with.
        var $advancedSearchForm;

        if ($match.length && ($advancedSearchForm = $match.find('form')).length) {

            // Advanced search is open

            // Replace whatever criteria is currently set. This is fine since our saved searches
            // are based on the unfiltered advanced search.
            storeFormInGlobalCriteria($advancedSearchForm, (loadedSavedSearch ? {'edit_search' : 'true', 'saved_search_id' : criteria.saved_search_id} : {}));

            // Return the advanced search form as-is. It has all the things.
            qs = $advancedSearchForm.serialize();
        } else {

            var $quickSearchForm = $('form.js-idx-search-header');
            var formCriteria = $quickSearchForm.serialize();
            if (window.criteria) {

                // No advanced search. Lets use our stored criteria
                qs = $.param(window.criteria);
            } else {

                // No criteria (we're probably not on an IDX page.) Lets fall back to
                qs = $match.data('ajax-current-request');
            }

            storeFormInGlobalCriteria($quickSearchForm, window.criteria);
        }

        // Make sure there aren't multiple subsequent ands. Make sure refine is not in the url
        qs = qs.replace(/&+/, '&').replace(/[?&]refine=[^?&]*/, '');

        if (window.criteria.hasOwnProperty('edit_search') && window.criteria.edit_search == 'true') {
            qs += '&edit_search=true&saved_search_id=' + window.criteria.saved_search_id;
        }

        if (refining || REW.settings.app == 'cms' || loadedSavedSearch) {
            // If we're already refining, or we're on a cms page (i.e. community or snippet), mark as a refined search.
            // This will prevent defaults from being loaded.
            qs += '&refine=true';
        }

        if (typeof $map !== 'undefined' && $map.hasClass('loaded')) {
            // Map Data
            var center = $map.REWMap('getCenter'),
                zoom = $map.REWMap('getZoom'),
                bounds = $map.REWMap('getBounds'),
                polygons = $map.REWMap('getPolygons'),
                radiuses = $map.REWMap('getRadiuses'),
                searchBounds = $('input[type="checkbox"][name="map[bounds]"]:first').closest('form').triggerHandler('checkMap') === 'Bounds';

            // Inject mapping
            qs += '&map[latitude]=' + encodeURIComponent(center.lat())
                + '&map[longitude]=' + encodeURIComponent(center.lng())
                + '&map[zoom]=' + encodeURIComponent(zoom)
                + '&map[ne]=' + encodeURIComponent(bounds.getNorthEast().toUrlValue())
                + '&map[sw]=' + encodeURIComponent(bounds.getSouthWest().toUrlValue())
                + '&map[polygon]=' + encodeURIComponent(polygons ? polygons : '')
                + '&map[radius]=' + encodeURIComponent(radiuses ? radiuses : '')
                + '&map[bounds]=' + encodeURIComponent(searchBounds ? 1 : 0);
        }

        qs = removeDuplicatesFromQueryString(qs);

        return qs;
    }

    REW.loadPanels = function(open) {
        var $match = $(advancedSearchTarget);

        if ($match.hasClass('loaded')) {
            if (open) {
                $(advancedSearchTarget).toggleClass('uk-hidden', false);
                // Make Sure Filters Are Up To Date
                $('.uk-form.idx-search-advanced').find('input,select').filter(':first').trigger('change');
            }
        } else if ($match.length) {
            var qs = getSearchArgumentsAndRewriteForms();
            var url = $match.data('ajax-url') + '&' + qs;

            // We need to ensure the last argument actually has a value, or the target will be passed in the request
            var $advanced = $('.js-advanced-search-main-trigger i');

            $advanced.addClass('uk-icon-spin').addClass('uk-icon-refresh').removeClass('uk-icon-angle-down').removeClass('uk-icon-angle-up');

            $.get(url, function (data) {
                $advanced.removeClass('uk-icon-spin').addClass('uk-icon-angle-down');
                firstSearch = false;
                $match.replaceWith(data);

                // Make Sure Duplicate Fields Are Updated With First Form's Values
                $('.js-idx-search-header').find('input,select').trigger('blur');

                $(REW).trigger('idx-refresh');
                // need to re-run autocomplete on panels
                $(REW).trigger('autocomplete-bind');
                $(REW).trigger('idx-submit', qs);

                if (open) {
                    $(advancedSearchTarget).toggleClass('uk-hidden', false).addClass('loaded');
                    // Make Sure Filters are Updated After the Initial Form Load
                    $('.uk-form.idx-search-advanced').find('input,select').filter(':first').trigger('change');
                }
            });
        }
    };

    function filterHidden(hidden) {
        if (!hidden) {
            REW.loadPanels(true);
        } else {
            $(advancedSearchTarget).toggleClass('uk-hidden', true);
        }

        // Toggle icon beside Filter
        var $icon = $('.js-advanced-search-trigger').find('i');

        $icon.each(function () {
            var $this = $(this);

            // Toggle icons under all triggers
            if (hidden) {
                if ($this.hasClass('uk-icon-angle-down')) {
                    $this.removeClass('uk-icon-angle-down').addClass('uk-icon-angle-up');
                }
            } else {
                if ($this.hasClass('uk-icon-angle-up')) {
                    $this.removeClass('uk-icon-angle-up').addClass('uk-icon-angle-down');
                }
            }
        });
    }

    $(document).on('click', '.js-advanced-search-trigger', function () {
        if ($(advancedSearchTarget).hasClass('uk-hidden')) {
            filterHidden(false);
        } else {
            filterHidden(true);
        }
    });

    filterHidden(true);

    /**
     * Gets forms, as a jQuery object if jqObject evaluates to true.
     *
     * @param jqObject
     * @returns {*}
     */
    function getForms(jqObject) {
        // Some of these are loaded via AJAX so we need to get them each time.
        // This MUST be a single selector or other things in this file will break.
        var selector = 'form.idx-search';

        return jqObject ? $(selector) : selector;
    }

    function countResults () {
        // Re-enable submit buttons, if it is disabled
        getForms(true).removeClass('submitting');
        var qs = getSearchArgumentsAndRewriteForms() + '&refine=true';

        var $match = $('.adv-search-panel');
        if ($match.length > 0) {
            var url = $match.data('ajax-url') + '&' + qs;
            $match.load(url, function () {
                var $filters = $match.find('.idx-filters');
                if ($filters.length) {
                    var $container = $('.filter-tags.live .idx-filters');
                    $filters.removeClass('uk-hidden');
                    $container.replaceWith($filters);

                    if (REW.SetPriceLabel) {
                        // Re-generate price label in the header. Use a timer so it gets done
                        // after the DOM is re-processed.
                        REW.SetPriceLabel();
                    }
                }

                $(REW).trigger('idx-refresh');
            });
        }
    }

    $(document).on('submit', getForms(), function (event) {
        event.preventDefault();
        var $this = $(this);

        refining = true;

        // Submit is disabled. This is set when a submit is in progress and unset if there are
        // any changes to the form.
        if ($this.hasClass('submitting')) {
            return;
        }

        $('form [data-lang-updating-results]').each(function () {
            var $this = $(this);

            // Set text to updating results
            $this.text($this.data('lang-updating-results'));
        });

        var args = getSearchArgumentsAndRewriteForms();
        $(REW).trigger('idx-submit', args);
        if (!REW.Helpers.isMapSearch()) {
            if (REW.qs.hasOwnProperty('edit_search')) {
                window.location.href = REW.url + '?' + args;
            }else{
                window.location.href = REW.settings.urls.search + '?' + getSearchArgumentsAndRewriteForms();
            }
        }else{
            // Close Header Price Label if Open
            $this.find('.wrapper').addClass('uk-hidden');
        }
    }).on('change', getForms() + ' input, ' + getForms() + ' select', function () {
        countResults();
    });

    $(document).on('click', '.js-idx-filter-remove-trigger', function () {
        var $this = $(this);
        var fields = $this.data('idx-tag');
        var live = $this.data('live-update') || false;

        // Remove filter
        $this.remove();

        if (typeof(fields) == 'object') {
            var searchCriteria = (typeof(REW.settings.criteria) != 'undefined') ? REW.settings.criteria : criteria;
            if ($.isArray(searchCriteria) || !searchCriteria) {
                searchCriteria = {};
            }

            for (var form_field in fields) {

                var selectedField = $('[name^="' + form_field + '"], [name^="map[' + form_field + '"]');
                if (selectedField.length) {
                    selectedField.each(function () {
                        var $this = $(this);
                        if ($this.val() == fields[form_field]) {
                            $this.removeAttr('selected').removeAttr('checked').not(':radio, :checkbox').val('');
                        }else if ($this.attr('name').indexOf('radius') !== -1) {
                            if (typeof $map !== 'undefined' && $map.hasClass('loaded')) {
                                $map.REWMap('clearRadiuses');
                            }
                            $this.val('');
                        }else if ($this.attr('name').indexOf('polygon') !== -1) {
                            if (typeof $map !== 'undefined' && $map.hasClass('loaded')) {
                                $map.REWMap('clearPolygons');
                            }
                            $this.val('');
                        }
                    });
                }

                if (fields[form_field] === searchCriteria[form_field]) {
                    delete searchCriteria[form_field];
                }
                if (typeof(searchCriteria[form_field]) != 'undefined') {
                    if (!$.isArray(searchCriteria[form_field])){
                        var fieldSplitTrim = searchCriteria[form_field].trim().split(',')
                            .map(function(item) { return item.trim(); })
                            .filter(function(n){ return n != ''; });

                        searchCriteria[form_field] = fieldSplitTrim;
                        var indx = $.inArray(fields[form_field], searchCriteria[form_field]);
                        if (indx > -1) {
                            searchCriteria[form_field].splice(indx, 1);
                        }
                    }
                }else{
                    // Could be a Map Filter
                    if (typeof(searchCriteria['map']) != 'undefined' && typeof(searchCriteria['map'][form_field]) != 'undefined') {
                        delete searchCriteria['map'][form_field];
                    }
                }
            }

            searchCriteria.refine = 'true';

            if (live) {
                // Recount
                $(REW).trigger('idx-submit', getSearchArgumentsAndRewriteForms());

            } else {
                // Mark as a refined search so the default criteria isn't reloaded.
                searchCriteria.refine = 'true';
                if ((REW.qs.hasOwnProperty('edit_search') && REW.qs.edit_search == 'true') || (REW.qs.hasOwnProperty('create_search') && REW.qs.create_search == 'true')) {
                    searchCriteria = $.extend(searchCriteria, REW.qs);
                    window.location = REW.url + (searchCriteria ? ('?' + $.param(searchCriteria)) : '');
                }else{
                    if (searchCriteria.hasOwnProperty('saved_search_id')) delete searchCriteria.saved_search_id;
                    window.location = REW.settings.urls.search + (searchCriteria ? ('?' + $.param(searchCriteria)) : '');
                }
            }
        }
    });

    // REWMOD Max.K(2016-09-19) fix for checkboxs and selects sync
    $(document).on('change', getForms() + ' input[type=checkbox]', function () {
        var $this = $(this);
        var form = $this.parents('form');
        // http://api.jquery.com/prop/
        getForms(true).not(form).find('input[type=checkbox][name="' + $this.attr('name') + '"][value="' + $this.val() + '"]').prop('checked', $this.prop('checked'));
        storeFormInGlobalCriteria($(advancedSearchTarget).find('form'));
    });

    $(document).on('change', getForms() + ' select', function () {
        var $this = $(this);
        var form = $this.parents('form');
        getForms(true).not(form).find('select[name="' + $this.attr('name') + '"]').val( $this.val() );
        storeFormInGlobalCriteria($(advancedSearchTarget).find('form'));
    });

    // Saved search handling
    var $container = $(document);
    $container.on('click', '.save-search, .save-search-email', function (event) {
        event.preventDefault();

        var email_results_immediately = $(this).attr('class').indexOf('save-search-email') > -1 ? 'true' : 'false';
        var search = $.extend(true, window.criteria, {
            view : $('#view-grid').hasClass('selected') ? 'grid' : 'list',
            email_results_immediately: email_results_immediately
        });

        // Update saved search title
        var $search_title = $('[name="search_title"]').last();
        var search_title = $search_title.val();
        if (search_title && search_title.length > 0) {
            search.search_title = search_title;
        }

        // Update saved search frequency
        var $frequency = $('[name="frequency"]').last();
        var frequency = $frequency.val();
        if (frequency && frequency.length > 0) {
            search.frequency = frequency;
        }
        search = $.extend(search, window.qs);

        // Save search criteria
        IDX.SaveSearch(search);

    }).on('click', '.delete-search', function (event) {
        var $this = $(this);
        event.preventDefault();

        UIkit.modal.confirm('Are you sure you want to delete this saved search?', function () {
            var href = $this.attr('href');
            if (href) {
                window.location = href;
            } else {
                IDX.deleteSearch ($this.attr('data-search-id'), $this.attr('data-redirect-to') || '/idx/');
            }
        });
    });

    //#REWMOD - Max.K - Back to top button
    var back2top_offset = 300,
        //grab the "back to top" link
        $back_to_top = $('#rew-back2top');

    if ($back_to_top.length) {
        //hide or show the "back to top" link
        $(window).scroll(function(){
            ( $(this).scrollTop() > back2top_offset ) ? $back_to_top.addClass('rew-back2top-visible') : $back_to_top.removeClass('rew-back2top-visible');
        });
    }

    $(document).on('click', '.js-panel-collapse-trigger', function () {
        var $this = $(this);
        var $parent = $this.closest('.idx-filter-col');
        var $panel = $parent.find('.toggle-panel');
        $panel.toggleClass('uk-hidden');
        $this.find('i').toggleClass('uk-icon-angle-down', !$panel.hasClass('uk-hidden'))
            .toggleClass('uk-icon-angle-right', $panel.hasClass('uk-hidden'));
    });
})();
