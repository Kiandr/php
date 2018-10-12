/* global $map */
/* global google */

// IDX builder panels
import 'common/idx-builder';

// Refine Form
var $form = $('#searchForm'),
    $counter = $('#ap-search-result-count'),
    $ss_dialog,
    suggested_title;

// Update Form Features Based on Form Value Changes
const updateSearch = function() {

    // Update Map Details
    if (typeof $map != 'undefined') {

        // Map Center
        var center = $map.REWMap('getCenter');
        if (center) {
            $form.find('input[name="map[latitude]"]').val(center.lat());
            $form.find('input[name="map[longitude]"]').val(center.lng());
        }

        // Zoom Level
        var zoom = $map.REWMap('getZoom');
        if (zoom) {
            $form.find('input[name="map[zoom]"]').val(zoom);
        }

        // Map Bounds
        var bounds = $map.REWMap('getBounds');
        $form.find('input[name="map[ne]"]').val(bounds ? bounds.getNorthEast().toUrlValue() : '');
        $form.find('input[name="map[sw]"]').val(bounds ? bounds.getSouthWest().toUrlValue() : '');

        // Polygon Searches
        var polygons = $map.REWMap('getPolygons'), $polygons = $form.find('input[name="map[polygon]"]');
        if (typeof polygons !== 'undefined') $polygons.val(polygons ? polygons : '');

        // Radius Searches
        var radiuses = $map.REWMap('getRadiuses'), $radiuses = $form.find('input[name="map[radius]"]');
        if (typeof radiuses !== 'undefined') $radiuses.val(radiuses ? radiuses : '');

        // Toggle Location Inputs
        $form.find('.location').prop('disabled', (
            $form.find('input[name="map[polygon]"]').val() != ''
            || $form.find('input[name="map[radius]"]').val() != ''
            || $form.find('input[name="map[bounds]"]').is(':checked')
        ));
    }

    // Update Result Counter
    $counter.html('Loading&hellip;');
    setTimeout(function() {
        $.ajax({
            'url'		: '/idx/inc/php/ajax/json.php?searchCount',
            'type'		: 'POST',
            'dataType'	: 'json',
            'data'		: $form.serialize(),
            'success'  : function (json) {
                if (typeof(json.count) != 'undefined' && json.count > 0) {
                    // Suggested Search Title
                    suggested_title = json.suggested_title ? json.suggested_title : '';
                    var properties_count = (Math.round(json.count).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ','));
                    $counter.html('<div class="-pad8" style="overflow: hidden">'
                        + '<strong>' + properties_count + ' Properties Found</strong>'
                        + ((json.count > 500)
                            ? ' - Please narrow your search to <strong>less than 500</strong> results to Save This Search'
                            : ' <a href="#" class="ap-create btn btn--positive R">Save This Search</a>'
                        )
                        + '</div>'
                    );
                } else {
                    $counter.html('No Properties Match Your Criteria');
                }
            }
        });
    }, 300);
};

// Listeners
$(window).on('load', function() {

    // Form Listeners
    $form.on('change', updateSearch).trigger('change');

    $form.find('input, textarea').on('keyup', function() {
        $form.trigger('change');
    });

    $form.find('a.delete').on('click', function() {
        $form.trigger('change');
    });

    // Map Listeners
    if (typeof $map != 'undefined') {

        var map_self = $map.REWMap('getSelf');

        // Update Form on Bounds Change
        google.maps.event.addListener($map.REWMap('getMap'), 'idle', function(){
            $form.trigger('change');
        });

        // Update Form on Radius Changes
        map_self.radiusControl.opts.onRefresh = (function(){
            $form.trigger('change');
        });

        // Update Form on Polygon Changes
        map_self.polygonControl.opts.onRefresh = (function(){
            $form.trigger('change');
        });

    }
});

$(document).on('click', '.ap-create', function(e) {
    e.preventDefault();

    $ss_dialog = $('<div style="overflow: hidden;"></div>')
        .html('<form method="post">'
            + '<div class="field">'
            + '<label style="width: auto" class="field__label">Search Title</label>'
            + '<input name="search_title" value="' + suggested_title + '" placeholder="' + suggested_title + '" style="width:100%;" maxlength="250" required>'
            + '</div>'
            + '<div class="field">'
            + '<label style="width: auto" class="field__label">Email Frequency</label>'
            + '<select name="frequency" style="width:100%;">'
            + '<option value="never">Never</option>'
            + '<option value="immediately">Immediately</option>'
            + '<option value="daily">Daily</option>'
            + '<option value="weekly" selected="">Weekly</option>'
            + '<option value="monthly">Monthly</option>'
            + '</select>'
            + '</div>'
            + '<div>'
            + '<button type="submit" class="btn btn--positive">Create Search</button>'
            + '</div>'
            + '</form>')
        .dialog({
            autoOpen: false,
            modal: true,
            width: 500,
            resizable: false,
            title: 'Save This Search',
        });
    $ss_dialog.dialog('open');

    $ss_dialog.find('button[type="submit"]').on('click', function(e) {
        e.preventDefault();

        var $this = $(this),
            ss_form_data = $this.closest('form').serializeArray(),
            search_form_data = $form.serializeArray(),
            search_data = [],
            error_text = [],
            notify;

        // Re-structure Data by Key
        if (ss_form_data.length > 0) {
            for (let i=0; i<ss_form_data.length; i++) {
                ss_form_data[ss_form_data[i].name] = ss_form_data[i].value;
            }
        }

        // Compile Relevant Search Data
        if (search_form_data.length > 0) {
            for (let i=0; i<search_form_data.length; i++) {
                if (!search_form_data[i].name.match('^panels\\[') && search_form_data[i].value != '') {
                    search_data.push(search_form_data[i]);
                }
            }
        }

        // Additional Search Data
        if (ss_form_data.search_title) {
            search_data.push({
                'name' : 'search_title',
                'value' : ss_form_data.search_title
            });
        }
        if (ss_form_data.frequency) {
            search_data.push({
                'name' : 'frequency',
                'value' : ss_form_data.frequency
            });
        }

        // Make Sure Search Frequency/Title Are Legit
        if ($.inArray(ss_form_data.frequency, ['never','immediately','daily','weekly','monthly']) < 0) {
            error_text.push('"' + ss_form_data.frequency + '" is not a valid frequency option.');
        }
        if (ss_form_data.search_title.length <= 0) {
            error_text.push('Please provide a search title.');
        }

        // Try to Save the Search
        if (!error_text.length) {

            var $spinner = $('<img src="/backend/img/ajax-loader.gif" style="position:fixed;top:50%;left:50%;z-index:99999;width:auto;">'),
                $d_body = $('body.idx-action_plan_search_form');
            $d_body.prepend($spinner);
            $d_body.find('.ui-dialog').hide();

            $.ajax({
                'url'      : '/idx/inc/php/ajax/json.php?saveSearch',
                'type'     : 'POST',
                'dataType' : 'json',
                'data'     : search_data,
                'success'  : function (json) {
                    if (typeof(json) == 'undefined' || !json) return;
                    // Success
                    if (json.success) {
                        top.location.reload(true);
                        // Error Occurred
                    } else if (json.error) {
                        // Error Notifications
                        error_text.push(json.error);
                    }
                }
            });
        }

        // Error Output
        if (error_text.length) {
            if (notify) notify.close();
            notify = $('#notifications').notify('create', 'notify-error', {
                title : 'An Error Has Occurred!',
                text: '<ul><li>' + error_text.join('</li><li>') + '</li></ul>'
            });
        }
        error_text = [];

    });
});
