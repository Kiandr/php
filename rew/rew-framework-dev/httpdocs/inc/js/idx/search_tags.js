(function () {
    'use strict';

    // IDX Search Tags
    $('.idx-search').on('click', 'a[data-idx-tag]', function () {
        var $tag = $(this)
            , data = $tag.data('idx-tag')
            , $form = $tag.closest('form')
  ;
        if (typeof data === 'object') {
            for (var field in data) {
                if (data.hasOwnProperty(field)) {

                    // Clear map radius
                    if (field === 'radius') {
                        if ($map && $map.length === 1) $map.REWMap('clearRadiuses');
                        $form.find(':input[name="map[radius]"]').val('');

                        // Clear map polygons
                    } else if (field === 'polygon') {
                        if ($map && $map.length === 1) $map.REWMap('clearPolygons');
                        $form.find(':input[name="map[polygon]"]').val('');

                    } else {
                        var value = data[field]
                            , $field = $form.find(':input[name="' + field + '"]')
      ;
                        if ($field.length > 0) {

                            // Update <input type="radio">
                            if ($field.attr('type') == 'radio') {
                                $field = $form.find(':radio[name^="' + field + '"][value="' + value + '"]');
                                if ($field.length > 0) $field.prop('checked', false);
                            }
                            // Update <input>
                            else if ($field.is('input')) {
                                var val = $field.val();
                                if (val === value) {
                                    $field.val('');
                                } else {
                                    $field.val(val.split(',').filter(function (val) {
                                        return value !== val.trim();
                                    }).join(',').trim());
                                }

                                // Clear <select>
                            } else if ($field.is('select')) {
                                $field.val('');

                            }

                        } else {

                            // Update <input type="checkbox">
                            $field = $form.find(':checkbox[name^="' + field + '"][value="' + value + '"]');
                            if ($field.length > 0) $field.prop('checked', false);

                        }

                        var $features = $('select[name="search_features[]"]');
                        var $option = $features.find('option[value="' + field + '"]');
                        if ($option.length > 0) {
                            $option.removeProp('selected');
                            $features.trigger('change');
                        }
                    }
                }
            }
        }

        // Hide [data-idx-tags] parent if last tag
        var $list = $tag.closest('[data-idx-tags]');
        if ($list.length === 1) {
            var $tags = $list.find('a[data-idx-tag]');
            $list.toggleClass('hidden', $tags.length === 1);
        }

        // Remove tag
        $tag.remove();
    });

})();




