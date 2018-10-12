(function () {
    'use strict';

    // @link http://stackoverflow.com/a/20056485/3597033
    $(document).on('DOMNodeInserted', '.pac-container', function () {
        $(this).find('.pac-item, .pac-item span').addClass('needsclick');
    });

    // Google Places Autocomplete
    var gautocomplete = false, componentTypes = {
        premise: 'long_name',
        route: 'long_name',
        locality: 'long_name',
        postal_code: 'long_name',
        street_number: 'long_name',
        administrative_area_level_1: 'short_name',
        administrative_area_level_2: 'long_name'
    };

    // Check if value is empty
    var isEmpty = function (value) {
        return !value || value.length < 1;
    };

    // Redirect to CMA page
    var handleRedirect = function ($form, $address) {
        var url = $form.attr('action'), params = [], address = $address.val();
        if (!isEmpty(address)) params.push($address.attr('name') + '=' + encodeURIComponent(address));
        window.location.href = url + (params.length > 0 ? '?' + params.join('&') : '');
    };

    // Load Google Places Autocomplete API
    var loadGooglePlacesApi = function () {
        REWMap.libraries.push('places');
        REWMap.loadApi(function () {
            gautocomplete = new google.maps.places.Autocomplete($address.get(0), {
                types: ['geocode'],
                componentRestrictions: {
                    country: REW.settings.lang.substring(REW.settings.lang.length - 2)
                }
            });
        });
    };

    // Get Google Places Autocomplete Values
    var getGooglePlaceDetails = function () {
        // Require autocomplete place
        if (!gautocomplete) return false;
        var place = gautocomplete.getPlace();
        if (!place) return false;
        // Gather address information
        var address = { full_address: place.formatted_address || place.name };
        var components = place.address_components;
        if (!components) return address;
        // Include address components
        $.each(components, function (i, component) {
            var compType = component.types[0];
            var compTypeName = componentTypes[compType];
            if (typeof compTypeName === 'string') {
                address[compType] = component[compTypeName];
            }
        });
        return address;
    };

    // Home valuation module's form
    var $form = $('#home-valuation');
    if ($form.length === 1) {

        // Form fields
        var $address = $form.find('input[name="adr"]');
        var $honeypot = $form.find('input[name="registration_type"]');
        var $email = $form.find('input[name="mi0moecs"]');

        // Enable Google Places autocomplette
        $address.one('focus', function () {
            loadGooglePlacesApi($form);
        });

        // Handle form submission
        $form.on('submit', function (e) {
            e.preventDefault();

            // Require a street address
            var address = $address.val();
            if (isEmpty(address)) {
                UIkit.modal.alert('Enter your street address');
                return false;
            }

            // Require an email address
            var email = $email.val();
            if (isEmpty(email)) {
                UIkit.modal.alert('Enter your email address');
                return false;
            }

            // Check if already processing
            if ($form.data('processing')) return false;
            $form.data('processing', true);

            // Get place components
            var place = getGooglePlaceDetails();
            if (!place) place = { full_address: address };

            // Street address
            var street = [place.street_number, place.route].join(' ').trim();
            if (place.premise) street = place.premise + (address ? ', ' + address : '');

            // Capture data
            var formData = {
                email: email,
                address: street,
                honeypot: $honeypot.val(),
                full_address: place.full_address,
                county: place.administrative_area_level_2,
                state: place.administrative_area_level_1,
                city: place.locality,
                zip: place.postal_code
            };

            // Track it
            $.ajax({
                url: '/idx/inc/php/ajax/html.php?module=home-valuation&options[ajax]=true',
                type: 'POST',
                data: formData,
                dataType: 'json',
            }).always(function () {
                $form.data('processing', false);
            }).done(function (data, textStatus, jqXHR) {
                handleRedirect($form, $address);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status === 0 || jqXHR.readyState === 0 || errorThrown === 'abort') return;
                handleRedirect($form, $address);
            });

        });

    }

})();