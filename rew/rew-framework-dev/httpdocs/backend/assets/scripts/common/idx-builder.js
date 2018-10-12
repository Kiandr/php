// Import script for property type/sub-type
import '../../../../inc/js/idx/search.js';

// Import rew_map plugin
import '../plugins/rew_map.js';

// Import Drive Time Panel Script
import '../../../../inc/js/idx/drive_time.js';

/* global IDX_BUILDER_MAP */

// Initialize map instance
const $map = $('#idx-builder-map');
if (typeof IDX_BUILDER_MAP === 'object') {
    $map.REWMap(IDX_BUILDER_MAP);
}

// IDX search panel list
const $panels = $('#idx-builder-panels').find('.idx-panels');
const $advanced = $panels.filter('.advanced');
const $select = $('select#new-search-panel');
const $list = $panels.not($advanced);

// D&D sorting
$panels.sortable({
    items: '.panel',
    handle: 'dt .handle',
    connectWith: '.idx-panels',
    helper: 'clone',
    opacity: '0.6'
});

// Handle action to toggle IDX panel visibility
$panels.on('change', 'select[data-panel-action="toggle"]', function () {
    const value = this.value;
    const hidden = value === 'hidden' ? 1 : 0;
    const display = value !== 'hidden' ? 1 : 0;
    const collapsed = value === 'collapsed' ? 1 : 0;
    const $panel = $(this).closest('.panel');
    const panel = $panel.data('panel');
    $panel.find('input[name="panels[' + panel + '][hidden]"]').val(hidden ? 1 : 0);
    $panel.find('input[name="panels[' + panel + '][display]"]').val(display ? 1 : 0);
    $panel.find('input[name="panels[' + panel + '][collapsed]"]').val(collapsed ? 1 : 0);
    return false;
});

// Handle action to expand IDX panel
$panels.on('click', 'a[data-panel-action="expand"]', function () {
    const $panel = $(this).closest('.panel');
    $panel.siblings().find('.content').hide();
    $panel.find('.content').toggle();
    return false;
});

// Handle action to delete IDX panel
$panels.on('click', 'a[data-panel-action="delete"]', function () {
    const $panel = $(this).closest('.panel');
    const $inputs = $panel.find(':input');
    const panel = $panel.data('panel');
    $panel.addClass('hidden');
    $inputs.prop('disabled', true);
    $inputs.prop('checked', false);
    $inputs.prop('selected', false);
    $inputs.not(':checkbox,:radio,:hidden').val('');
    $select.find(`option[value="${panel}"]`).prop('disabled', false);
    if (panel === 'polygon') $map.REWMap('clearPolygons');
    if (panel === 'radius') $map.REWMap('clearRadiuses');
    return false;
});

// Clear Drive Time panel when input is emptied
$('.drivetime-ac-search').on('keyup', function () {
    $map.REWMap('clearPolygons');
});

// Autocomplete inputs
$('#idx-builder-panels input.autocomplete').each(function () {
    var $this = $(this), multiple = $this.hasClass('single') ? false : true;
    $this.autocomplete({
        source: function (request, response) {
            if (multiple) {
                request.term = request.term.split(/,\s*/).pop();
            }
            $.getJSON('/idx/inc/php/ajax/json.php?limit=10&search=' + $this.attr('name'), {
                q: request.term,
                cache: false,
                search_city: function () {
                    var search_city = $('select[name="search_city"]');
                    if (search_city.length == 1) {
                        return search_city.val();
                    } else {
                        return '';
                    }
                },
                feed: function () {
                    var $feed = $(':input[name="feed"]');
                    if ($feed.length == 1) {
                        return $feed.val();
                    } else {
                        return '';
                    }
                }
            }, function (data) {
                var parsed = [];
                var rows = data.options ? data.options : [];
                for (var i = 0; i < rows.length; i++) {
                    var row = $.trim(rows[i].title);
                    if (row) {
                        row = row.split('|');
                        parsed.push({
                            value: row[0],
                            label: row[0]
                        });
                    }
                }
                response(parsed);
            });
        },
        focus: function () {
            return false;
        },
        select: function (event, ui) {
            if (multiple) {
                var terms = this.value.split(/,\s*/);
                terms.pop();
                terms.push(ui.item.value);
                terms.push('');
                this.value = terms.join(' ');
                return false;
            }
        }
    });
});

// Disable hidden panels
$panels.find('.panel.hidden').find(':input').prop('disabled', true);

// Add new search panel to list of search criteria
$('#add-search-panel').on('click', function () {
    const panel = $select.val();
    if (panel.length < 0) return false;

    // Remove panel from select list
    $select.find('option:selected').prop('disabled', true);
    $select.val('');

    // Add panel to bottom of list
    const $panel = $(`#panel-${panel}`);
    $list.append($panel);

    // Show search pnale and enable input fields
    $panel.removeClass('hidden collapsed').show();
    $panel.find(':input').prop('disabled', false);
    $panel.find(`input[name="panels[${panel}][hidden]"]`).val(0);
    $panel.find(`input[name="panels[${panel}][display]"]`).val(1);
    $panel.find(`input[name="panels[${panel}][collapsed]"]`).val(0);

});

// Toggle advanced search options
$advanced.on('click', 'h2', function () {
    var $extras = $advanced.find('.advanced-panels'), open = $extras.hasClass('hidden');
    $advanced.find('.ui-icon').removeClass(open ? 'ui-icon-plusthick' : 'ui-icon-minusthick');
    $advanced.find('.ui-icon').addClass(open ? 'ui-icon-minusthick' : 'ui-icon-plusthick');
    if (open) {
        $extras.removeClass('hidden').stop().slideDown();
    } else {
        $extras.removeClass('hidden').stop().slideUp(function () {
            $extras.addClass('hidden');
        });
    }
});

// Handle updating hidden inputs on form submit
$('#idx-builder-form').on('submit', function () {
    const $form = $(this);

    // Split Panels
    const split = $list.find('.panel').not('.hidden').length;
    $form.find('input[name="split"]').val(split);

    // Update hidden map form fields
    if (typeof $map !== 'undefined') {
        const zoom = $map.REWMap('getZoom');
        const center = $map.REWMap('getCenter');
        const bounds = $map.REWMap('getBounds');
        const polygons = $map.REWMap('getPolygons');
        const radiuses = $map.REWMap('getRadiuses');
        $form.find('input[name="map[latitude]"]').val(center.lat());
        $form.find('input[name="map[longitude]"]').val(center.lng());
        $form.find('input[name="map[ne]"]').val(bounds.getNorthEast().toUrlValue());
        $form.find('input[name="map[sw]"]').val(bounds.getSouthWest().toUrlValue());
        $form.find('input[name="map[polygon]"]').val((polygons ? polygons : ''));
        $form.find('input[name="map[radius]"]').val((radiuses ? radiuses : ''));
        $form.find('input[name="map[zoom]"]').val(zoom);

    }
});

// Handle min/max range inputs
$panels.find('.range').each(function () {
    const $range = $(this);
    const $min = $range.find('.min select');
    const $max = $range.find('.max select');
    if ($min.length > 0 && $max.length > 0) {
        $min.on('change', function () {
            const min = parseInt($min.val());
            const max = parseInt($max.val());
            if (min > max) $max.val('');
            return true;
        });
        $max.on('change', function () {
            const min = parseInt($min.val());
            const max = parseInt($max.val());
            if (min > max) $min.val('');
            return true;
        });
    }
});
