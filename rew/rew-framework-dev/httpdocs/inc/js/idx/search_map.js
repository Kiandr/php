//  Setup map instance
var $map = $('#idx-map-search').REWMap($.extend(true, mapOptions || {}, {
    onInit : function () {
        if (typeof $form === 'object') $form.trigger('submit');
        if (typeof $searchBounds === 'object') $searchBounds.trigger('bounds');
    },
    manager : {
        cluster : true
    },
    polygonControl : {
        onRefresh : function () {
            if (typeof $form === 'object') {
			    if (!this.hasSearches()) $form.find('input[name="map[polygon]"]').val('');
                $form.trigger('toggleLocations');
            }
        }
    },
    radiusControl : {
        onRefresh : function () {
            if (typeof $form === 'object') {
                if (!this.hasSearches()) $form.find('input[name="map[radius]"]').val('');
                $form.trigger('toggleLocations');
            }
        }
    }
}));