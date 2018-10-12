(function () {

    // Directions
    var $directions = $('#map-directions');

    // Switch Map View
    var $views = $('.views a').on('click', function () {
        var $this = $(this), href = $this.attr('href');
        // Map & Directions
        if (href === '#map') {
            $birdseye.addClass('hidden');
            $streetview.addClass('hidden');
            $map.removeClass('hidden');
            $directions.removeClass('hidden');

            // Google Streetview
        } else if (href === '#streetview') {
            $map.addClass('hidden');
            $birdseye.addClass('hidden');
            $directions.addClass('hidden');
            $streetview.removeClass('hidden');
            $map.REWMap('getTooltip').hide(true);

            // Bird's Eye View
        } else if (href === '#birdseye') {
            $map.addClass('hidden');
            $streetview.addClass('hidden');
            $directions.addClass('hidden');
            $birdseye.removeClass('hidden');
            $map.REWMap('getTooltip').hide(true);

            // Get Local
        } else if (href === '#local') {
            $birdseye.addClass('hidden');
            $streetview.addClass('hidden');
            $directions.addClass('hidden');
            $map.removeClass('hidden');
            $.Window({ iframe : URL_LOCAL });
            return false;

        }
        // Switch Current
        $views.parent().not($this.parent().addClass('current')).removeClass('current');
        return false;
    });

    // Show Map Views
    var $v = $views.parent().not('.hidden');
    if ($v.length > 1) $v.closest('ul').removeClass('hidden');

    // Detect Streetview
    var $streetview = $('#map-streetview');
    if ($streetview.length > 0) {
        var streetview = new REWMap.Streetview({
            el: $streetview.get(0),
            lat: MAP_LATITUDE,
            lng: MAP_LONGITUDE,
            onSuccess : function (data) {
                $views.filter('[href="#streetview"]').parent().removeClass('hidden').closest('ul').removeClass('hidden');
            }
        });
    }

    // Birds Eye View
    var $birdseye = $('#map-birdseye');
    if ($birdseye.length > 0) {
        var birdseye = new REWMap($birdseye, $.extend(true, {}, MAP_OPTIONS, {
		    type: 'satellite',
		    zoom: 18
        }));
    }

    // Load Map
    var $map = $('#map-canvas').REWMap($.extend(true, MAP_OPTIONS, {
        onInit : function () {

            // Setup Directions
            var $displayDirections = $('#directions');
            if ($displayDirections.length > 0) {

                // Directions Control
                var directions = new REWMap.Directions({
                    renderer : {
                        map: this.getMap(), // Render on Map
                        panel: $displayDirections.get(0) // Render to DOM Element
                    },
                    onSuccess: function () {
                        $displayDirections.find('.msg.negative').remove();
                        $print.removeClass('hidden');
                    },
                    onFailure: function (error) {
                        $displayDirections.html('<p class="msg negative">' + error + '</p>');
                        $print.addClass('hidden');
                    }
                });

                // Form Submit
                var $form = $('#map-directions').on('submit', 'form', function () {
                    var from = $form.find('input[name="from"]').val(), to = $form.find('input[name="to"]').val();
                    directions.getDirections(from, to);
                    return false;
                });

                // Print Button
                var $print = $('<a href="javascript:void(0);" class="hidden">Print Directions</a>').on('click', function () {
                    var w = window.open('about:blank');
                    w.document.write($displayDirections.html());
                    w.document.close();
                    w.focus();
                    w.print();
                });

                $('#directions').append($print);

            }

        }
    }));

})();