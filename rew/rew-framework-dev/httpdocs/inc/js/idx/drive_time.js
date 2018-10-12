/* global google */

/* <script> */
(function () {
    var place_valid = false;

    function resetDriveTimeValues($form) {
        // Drive Time Location Fields
        $form.find('input[name="place_zip"]').val('');
        $form.find('input[name="place_lat"]').val('');
        $form.find('input[name="place_lng"]').val('');
        $form.find('input[name="place_zoom"]').val('');
        $form.find('input[name="place_adr"]').val('');
        // Polygon + Map Altering Fields
        $form.find('input[name="map[longitude]"]').val('');
        $form.find('input[name="map[longitude]"]').val('');
        $form.find('input[name="map[latitude]"]').val('');
        $form.find('input[name="map[polygon]"]').val('');
        $form.find('input[name="map[zoom]"]').val('');
    }

    // Google Places Autocomplete
    REWMap.libraries.push('places');
    REWMap.loadApi(function () {

        // Backend
        var backend = window.location.pathname.indexOf('/backend/') >= 0;
        if (backend) {
            var $form = $('#idx-builder-form');
            // Frontend
        } else {
            var $form = $('.idx-search');
        }

        var $ac_input = $('.drivetime-ac-search'),
            $place_lat = $form.find('input[name="place_lat"]'),
            $place_lng = $form.find('input[name="place_lng"]'),
            autocomplete,
            input = $ac_input.get(0),
            driveTimeToolTip = document.querySelector('.drivetime-ac-search-tooltip');


        autocomplete = new google.maps.places.Autocomplete(input, {
            'types' : ['geocode'],
            'componentRestrictions' : {
                'country' : ($('html').attr('lang') === 'en-CA' ? 'CA' : 'US')
            }
        });

        // Resolves issue with un-clickable place options on mobile devices
        $(document).on({
            'DOMNodeInserted': function() {
                $('.pac-item, .pac-item span', this).addClass('needsclick');
            }
        }, '.pac-container');

        // Needed to prevent submit when using keys to select Google Places AC value(s)
        $ac_input.on('change', function () {
            resetDriveTimeValues($form);
        });
        $ac_input.on('keydown', function (e) {
            if (e.keyCode === 13) {
                e.preventDefault();
            }
            if (!$(this).val().length) return;
            resetDriveTimeValues($form);
        });

        // Show drivetime tooltip
        $ac_input.on('invalid', function (e) {
            e.preventDefault();
            this.classList.add('invalid');
            driveTimeToolTip.classList.remove('hidden');
        });

        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            driveTimeToolTip.classList.add('hidden');
            $ac_input.removeClass('invalid');
            var place = autocomplete.getPlace();
            if (typeof place !== 'undefined') {
                var includeZip = place.address_components[place.address_components.length - 1].long_name.length === 5
                    ? place.address_components[place.address_components.length - 1].long_name
                    : place.address_components[place.address_components.length - 2].long_name;

                // Update Hidden Inputs for Tracking Google Places Result Parts
                $form.find('input[name="place_zip"]').val(includeZip);
                $place_lat.val(place.geometry.location.lat());
                $place_lng.val(place.geometry.location.lng());
                $form.find('input[name="map[latitude]"]').val(place.geometry.location.lat());
                $form.find('input[name="map[longitude]"]').val(place.geometry.location.lng());
                $form.find('input[name="place_adr"]').val(place.formatted_address);
            }

            if ($ac_input.val() === '') return;
        });

        $form.on('submit', function (e) {
            place_valid = ($place_lat.val().length > 0 && $place_lng.val().length > 0);
            if($ac_input.val() != '' && !place_valid) {
                e.preventDefault();
                $ac_input.trigger('invalid');
            }
            // updating the zoom input based on the selected travel duration
            var dt_duration = $form.find('select[name="dt_travel_duration"]').val(),
                dt_zoom = 13;

            if (dt_duration >= 75) {
                dt_zoom = 8;
            } else if (dt_duration >= 45) {
                dt_zoom = 9;
            } else if (dt_duration >= 30) {
                dt_zoom = 10;
            } else if (dt_duration >= 15) {
                dt_zoom = 12;
            }

            $form.find('input[name="place_zoom"]').val(dt_zoom);
        });

    });
})();
/* </script> */