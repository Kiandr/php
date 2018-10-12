$(document).ready(function(){
    'use strict';

    var $module       = $('.cma-property-valuation[data-mapping]');
    if (!$module || $module.length == 0) return; // check if we're on correct page

    // Module vars
    var $map
        , $results      = $module.find('.matches')
        , $message      = $module.find('.ac-message')
        , $input        = $module.find('.ac-search')
        , $form         = $module.find('form.js-section-b')
        , $feed         = $form.find('input[name="feed"]')
        , $min_price    = $module.find('.min_price')
        , $max_price    = $module.find('.max_price')
        , $avg_price    = $module.find('.avg_price')
        , firstRun      = true
        , estimatedPrice = 0
        , searchCriteria = {
            'radius' : (typeof Locale === 'object' && Locale.lang == 'en-CA' ? (2.5 * 0.621371) : 1.5)
        }
        , searchPlace
        , autocomplete
        , xhr
 ;

    // Returns a formatted-for-humans number - eg: 500k, 1M, 1.5B
    var shortNumber = function (num) {
        var val = Number(String(num).replace(/\$|,/g, '')); // strip it
        if (isNaN(val)) return num;
        var t = (val / 1000000000000).toFixed(1) // trillion
            , b = (val / 1000000000).toFixed(1) // billions
            , m = (val / 1000000).toFixed(1) // millions
            , k = (val / 1000).toFixed() // thousands
  ;
        if (t >= 1) {
            return t + 'T';
        } else if (b >= 1) {
            return b + 'B';
        } else if (m >= 1) {
            return m + 'M';
        } else if (k >= 1) {
            return k + 'k';
        }
        return val;
    };

    // Google Places Autocomplete
    REWMap.libraries.push('places');
    REWMap.loadApi(function () {

        // Geolocation API
        if (navigator.geolocation && $input.val().length < 1) {
            //$message.html('Attempting to retrieve your location...').removeClass('hidden');
            navigator.geolocation.getCurrentPosition(function (pos) {
                $input.trigger('blur');
                //$message.html('Looking up address...').removeClass('hidden');
                var coords = pos.coords
                    , lat = coords.latitude
                    , lng = coords.longitude
                    , latlng = new google.maps.LatLng(lat, lng)
                    , geocoder = new google.maps.Geocoder()
    ;
                geocoder.geocode({'latLng': latlng}, function(results, status) {
                    $message.html('').addClass('uk-hidden');
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[1]) {
                            $input.val(results[1].formatted_address);
                            setLocation({
                                'adr' : results[1].formatted_address,
                                'lat' : lat,
                                'lng' : lng
                            });
                        } else {
                            alert('No results found');
                        }
                    } else {
                        alert('Geocoder failed due to: ' + status);
                    }
                });
            }, function (error) {
                $message.html('Unable to retrieve your location').removeClass('html');
            });
        }

        // Extra map options
        var mapOptions = {
            'backgroundColor' : '#222',
            'mapTypeControl' : false,
            'styles' : [{
                'stylers' : [
                    { 'visibility' : 'simplified' }
                ]
            }, {
                'elementType'       : 'labels',
                'stylers'   : [{
                    'visibility'    : 'off'
                }]
            }, {
                'featureType'       : 'administrative',
                'stylers'   : [{
                    'visibility'    : 'on'
                }]
            }, {
                'featureType'       : 'road',
                'stylers'   : [{
                    'visibility'    : 'on'
                }]
            }, {
                'featureType'       : 'poi',
                'stylers'   : [{
                    'visibility'    : 'on'
                }]
            }, {
                'featureType'       : 'transit',
                'stylers'   : [{
                    'visibility'    : 'off'
                }]
            }]
        };

        // Google Places Auto-Complete for CMA Feature
        var input = $input.get(0);
        autocomplete = new google.maps.places.Autocomplete(input, {
            'types' : ['geocode'],
            'componentRestrictions' : {
                'country' : (window.REW.settings.lang == 'en-CA' ? 'CA' : 'US')
            }
        });

        // Predict Typed in Place
        var predictLocation = function (input, callback) {
            var service = new google.maps.places.AutocompleteService();
            service.getPlacePredictions({
                'input' : input,
                'types' : ['geocode'],
                'componentRestrictions' : {
                    'country' : (window.REW.settings.lang == 'en-CA' ? 'CA' : 'US')
                }
            }, function (predictions, status) {
                if (status != google.maps.places.PlacesServiceStatus.OK) {
                    $message.html('Your location "' + $('<span />').text(input).html() + '" could not be found. Please try another address.').removeClass('uk-hidden');
                    //alert(status);
                    return;
                }
                if (predictions.length > 0) {
                    var el = document.createElement('div'), places = new google.maps.places.PlacesService(el);
                    places.getDetails({
                        'reference' : predictions[0].reference
                    }, function (place, status) {
                        if (status != google.maps.places.PlacesServiceStatus.OK) {
                            $message.html('Your location "' + $('<span />').text(input).html() + '" could not be found. Please try another address.').removeClass('uk-hidden');
                            //alert(status);
                            return;
                        }
                        callback(place);
                    });
                }
            });
        };

        // Search for properties
        var setLocation = function (place) {

            // Set options
            searchPlace = place.adr;

            // Radius search
            var radiusSearch = {
                'radius' : searchCriteria.radius,
                'lat' : place.lat,
                'lng' : place.lng,
                'edit' : true
            };

            // Map markers
            var _markers = [{
                'title'         : place.adr,
                'lat'           : place.lat,
                'lng'           : place.lng,
                'icon'          : window.REW.settings.urls.skin + '/img/map-flag.png',
                'iconWidth'     : 22,
                'iconHeight'    : 25,
                'zIndex'        : 1000
            }];

            // Map is loaded
            if ($map) {
                // Clear map
                var map = $map.REWMap('getSelf'), gmap = map.getMap();
                map.clear();
                map.clearPolygons();
                map.clearRadiuses();
                map.load(_markers);
                // Load radius search and re-position
                map.radiusControl.load([radiusSearch]);
                var radius = map.radiusControl.searches[0];
                if (radius) gmap.fitBounds(radius.getBounds());
                return;
            }

            // Load Google Map
            $map = $module.find('.map').REWMap({
                'center' : {
                    'lat' : place.lat,
                    'lng' : place.lng
                },
                'manager' : {
                    'stack' : false,
                    'bounds' : false,
                    'markers' : _markers
                },
                'radiuses' : [radiusSearch],
                'polygonControl' : {
                    'el' : '#cma-polygon',
                    'onDelete' : function (radius) {

                        // Update results
                        $form.trigger('change');

                    },
                    'onDraw' : function (polygon) {

                        // Point was added
                        google.maps.event.addListener(polygon.getPath(), 'set_at', function() {
                            $form.trigger('change');
                        });

                        // Point was added
                        google.maps.event.addListener(polygon.getPath(), 'insert_at', function() {
                            $form.trigger('change');
                        });

                        // Point was removed
                        google.maps.event.addListener(polygon.getPath(), 'remove_at', function() {
                            $form.trigger('change');
                        });

                        // Polygon drawn
                        $form.trigger('change');

                    }
                },
                'radiusControl' : {
                    'el' : '#cma-radius',
                    'onDelete' : function (radius) {

                        // Update results
                        $form.trigger('change');

                    },
                    'onDraw' : function (radius) {

                        // Radius has been moved
                        google.maps.event.addListener(radius, 'center_changed', function () {
                            $form.trigger('change');
                        });

                        // Radius has changed
                        google.maps.event.addListener(radius, 'radius_changed', function () {
                            $form.trigger('change');
                        });

                        // Radius drawn
                        $form.trigger('change');

                    }
                },
                'onInit' : function () {

                    // Fit the radius
                    var gmap = this.getMap()
                        , radiusControl = this.radiusControl
                        , radius = (radiusControl.hasSearches() ? radiusControl.getSearches()[0] : false)
     ;
                    if (radius) gmap.fitBounds(radius.getBounds());

                    // Place drawing controls on map
                    var $mapTools = $module.find('.js-map-draw-controls').removeClass('uk-hidden'), mapTools = $mapTools.get(0);
                    gmap.controls[google.maps.ControlPosition.TOP_RIGHT].push(mapTools);

                    // Update results
                    //$form.trigger('change');

                },
                'scrollwheel' : false,
                'mapOptions' : mapOptions
            });

        };

        // Place selected
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            if ($input.val() === '') return;
            $message.html('').addClass('uk-hidden');
            var place = autocomplete.getPlace();
            if (place) {

                // Require location
                var geo = place.geometry;
                if (geo) {

                    // Location coordinates
                    var geo = place.geometry;
                    if (!geo) return;
                    var latlng = geo.location;
                    setLocation({
                        'adr' : place.formatted_address,
                        'lat' : latlng.lat(),
                        'lng' : latlng.lng()
                    });

                } else {

                    // Attempt to find location
                    predictLocation(place.name, function (place) {
                        var geo = place.geometry;
                        if (!geo) return;
                        var latlng = geo.location;
                        setLocation({
                            'adr' : place.formatted_address,
                            'lat' : latlng.lat(),
                            'lng' : latlng.lng()
                        });
                    });

                }

                // Try to find typed in location
            } else if (input.value.length > 0) {
                predictLocation(input.value, function (place) {
                    var geo = place.geometry;
                    if (!geo) return;
                    var latlng = geo.location;
                    setLocation({
                        'adr' : place.formatted_address,
                        'lat' : latlng.lat(),
                        'lng' : latlng.lng()
                    });
                });

            }
        });

        // Google places autocomplete
        $module.find('.ac-locate').on('click', function () {
            google.maps.event.trigger(autocomplete, 'place_changed');
            return false;
        }).trigger('click');

        // Refine search results
        $form.on('change', function () {

            // First run
            if (firstRun && !searchPlace) {
                firstRun = false;
                return;
            }
            firstRun = false;

            if ($(this).data('capture') === 1) {
                $('a.cta-link').trigger('click');
                return true;
            }
            // Loading...
            $min_price.text('...');
            $max_price.text('...');
            $avg_price.text('...');

            // Set criteria
            var $this = $(this), $subtype = $form.find('select[name="subtype"]');
            $.extend(searchCriteria, {
                //'radius'  : $this.find('select[name="radius"]').val(),
                'type'      : $form.find('select[name="type"]').val(),
                'subtype'   : $subtype.is(':disabled') ? '' : $subtype.val(),
                'beds'      : $form.find('select[name="beds"]').val(),
                'baths'     : $form.find('select[name="baths"]').val(),
                'sqft'      : $form.find('select[name="sqft"]').val()
            });

            // Search for properties
            if (xhr) xhr.abort();
            xhr = $.ajax({
                'url' : '/idx/inc/php/ajax/html.php?module=property-valuation',
                'dataType' : 'json',
                'type' : 'get',
                'data' : {
                    'feed'  : $feed.val(),
                    'options' : {
                        'input'             : $input.val(),
                        'place'             : searchPlace,
                        'search'            : searchCriteria,
                        'radiuses'          : $map && $map.REWMap('getRadiuses'),
                        'polygons'          : $map && $map.REWMap('getPolygons'),
                        'results'           : ($results.length === 1),
                        'defaults.location' : defaultLocation
                    }
                },
                'success' : function (data) {
                    if (data) {

                        // Price range
                        var total = shortNumber(data.total);
                        estimatedPrice = data.avg_price;
                        $min_price.text(data.min_price > 0 ? '$' + shortNumber(data.min_price) : 'N/A');
                        $max_price.text(data.max_price > 0 ? '$' + shortNumber(data.max_price) : 'N/A');
                        $avg_price.text(data.avg_price > 0 ? '$' + shortNumber(estimatedPrice) : 'N/A');

                        // Search results
                        var results = data.results;
                        var $listings_container = $results.find('.js-show-idx-properties');
                        $listings_container.html(results);
                        REW.activateTarget('view-grid',$listings_container);
                        $results.removeClass('uk-hidden');//.Images({ onLoad : false });
                        $results.find('.view-all').attr('href', data.url).text('See all ' + shortNumber(total) + ' matches').toggleClass('uk-hidden', (data.total < 2));

                        // Adjust price based on condition
                        adjustEstimate.call($condition.get(0));

                    }
                }
            });

        }).trigger('change');

    });

    // Disable submit on enter
    $form.on('keypress', 'input', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 13) return false;
        return true;
    });

    // Disable form submit
    $form.on('submit', function () {
        return false;
    });

    // Change estimate based on property condition
    var $condition = $form.find('select[name="condition"]'), adjustEstimate = function () {
        var $this = $(this), value = $this.val();
        if (estimatedPrice < 1) return false;
        var adjustedPrice = (estimatedPrice * value) / 100;
        if (adjustedPrice < 1) return false;
        $avg_price.text('$' + shortNumber(adjustedPrice));
    };
    $condition.on('change', adjustEstimate);

    var cmaPopup;
    // Bring up the cma form
    $module.on('click', 'a.cta-link', function () {
        var $this = $(this)
            , title = $this.text()
            , $subtype = $form.find('select[name="subtype"]')
            , data = $.param({
                'address'   : $input.val(),
                'feed'      : $feed.val(),
                'type'      : $form.find('select[name="type"]').val(),
                'subtype'   : $subtype.is(':disabled') ? '' : $subtype.val(),
                'beds'      : $form.find('select[name="beds"]').val(),
                'baths'     : $form.find('select[name="baths"]').val(),
                'sqft'      : $form.find('select[name="sqft"]').find('option:selected').text(),
                'condition' : $form.find('select[name="condition"]').find('option:selected').text(),
                'property_valuation'    : window.location.href.split('?')[0] + '?' + $form.serialize(),
                'comparable_properties' : $results.find('.view-all').filter(':visible').attr('href')
            })
  ;
        if (cmaPopup) {
            UIkit.modal(cmaPopup).show();
        }
        else {
            // Submit cma form
            $.ajax({
                'url'       : '/idx/inc/php/ajax/html.php?module=cms-snippet&options[name]=form-cma',
                'success'   : function (html) {

                    cmaPopup = $('<div class="uk-modal cma-popup"><div class="uk-modal-dialog"><a class="uk-modal-close uk-close"></a><div class="container cma-popup-container"><div class="modal-header bound uk-modal-header"><h1>'+title+'</h1></div><div class="modal-body bound uk-modal-body">' + html + '</div></div></div></div>').appendTo('body');

                    cmaPopup.on('submit', 'form', function () {
                        $.ajax({
                            'url'       : '/idx/inc/php/ajax/html.php?module=cms-snippet&options[name]=form-cma&submit',
                            'type'      : 'post',
                            'data'      : $(this).serialize() + '&' + data,
                            'success'   : function (html) {
                                cmaPopup.find('.uk-modal-body').html(html);
                            }
                        });
                        return false;
                    });
                    UIkit.modal(cmaPopup).show();
                }
            });
        }
        return false;
    });


    // Equal heights map canvas
    var fixHeight = function () {
        $module.find('.map').height($module.find('.js-section-b').height() - $module.find('.estimate-values').height());
    }; fixHeight();

    // Update property sub-types
    $form.find('select[name="type"]').on('change', function () {
        var $this = $(this), value = $this.val();
        var pid = Math.random() * 5, $subtypes = $form.find('select[name="subtype"]').data('pid', pid);
        if ($subtypes.length) {
            // No type selected
            if (value.length === 0) {
                $subtypes.closest('.field').addClass('uk-hidden');
                $subtypes.prop('disabled', true);
                fixHeight();
                return true;
            }
            // Load sub-types
            $.ajax({
                'url' : '/idx/inc/php/ajax/json.php?searchTypes',
                'type' : 'POST',
                'dataType' : 'json',
                'data' : {
                    'pid' : pid,
                    'feed' : $feed.val(),
                    'search_type' : value
                },
                'success' : function (json) {
                    if (!json || json.pid != $subtypes.data('pid')) return;
                    if (json.returnCode == 200) {
                        // No available sub-types
                        if (json.options.length === 0) {
                            $subtypes.closest('.field').addClass('uk-hidden');
                            $subtypes.prop('disabled', true);
                        } else {
                            // Update available sub-types
                            var html = '<select name="subtype"><option value="">...</option>'
                                , subtype = $subtypes.val()
                                , len = json.options.length
                                , i = 0
       ;
                            while (i < len) {
                                var option = json.options[i], checked = (subtype == option.value) ? ' selected' : '';
                                html += '<option value= "' + option.value + '"' + checked + '>' + option.title + '</option>';
                                i++;
                            }
                            $subtypes.closest('.field').removeClass('uk-hidden');
                            $subtypes.replaceWith(html);
                        }
                        // Fix height
                        fixHeight();
                    }
                }
            });
        }
        return true;
    }).trigger('change');

});