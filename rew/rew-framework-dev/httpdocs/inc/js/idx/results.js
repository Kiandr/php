//View toggle removes office and disclaimer when compliance rule met
window.onload = function() {
    if(document.getElementById('gridViewOffice')) {

        var g = document.getElementById('gridViewOffice');
        var d = document.getElementById('detailedViewOffice');

        if($('#gridView').hasClass('current')) {
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

        if($('#gridView').hasClass('current')) {
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

var $map;
var $mapList = {};
(function () {
    if (typeof REWMap !== "undefined") {
        for(var options in mapOptions) {
            var $entry = $('#listings-map-'+options).REWMap($.extend(mapOptions[options] || {}, {
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
            $mapList[options || feed] = $entry;
        }
    }
    $map = $mapList[feed];
})();
