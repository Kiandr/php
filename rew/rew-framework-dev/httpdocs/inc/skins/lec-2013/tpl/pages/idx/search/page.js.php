/* <script> */
(function () {

	// Toggle Sidebar
	var $toggle = $('.action-show-sidebar').on('click', function() {
		var $this = $(this), $sidebar = $('#sidebar');
		if ($sidebar.hasClass('hidden-tablet')) {
			$sidebar.removeClass('hidden-tablet');
			$this.text('Less Options');
		} else {
			$sidebar.addClass('hidden-tablet');
			$this.text($this.data('text'));
		}
		return false;
	});
	$toggle.data('text', $toggle.text());

	// Bind to Form Submit
	var $form = $('form.idx-search').bind('submit', function () {

	    // Save Map Details
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

	    // Quick Search Fields
	    var $qs = $('#search_mast')
	    	, searchLocation = $qs.find('input[name="search_location"]').val()
	    	, minPrice	= $qs.find('select[name="minimum_price"]').not(':disabled').val()
	    	, maxPrice	= $qs.find('select[name="maximum_price"]').not(':disabled').val()
	    	, minRent	= $qs.find('select[name="minimum_rent"]').not(':disabled').val()
	    	, maxRent	= $qs.find('select[name="maximum_rent"]').not(':disabled').val()
	    ;

	    // Set Search Values
	    $form.find('input[name="search_location"]').val(searchLocation);
	    $form.find('input[name="minimum_price"]').val(minPrice);
	    $form.find('input[name="maximum_price"]').val(maxPrice);
	    $form.find('input[name="minimum_rent"]').val(minRent);
	    $form.find('input[name="maximum_rent"]').val(maxRent);

	});

	// Refine Search
	$('#btn-refine').on('click', function () {
		$form.trigger('submit');
		return false;
	});

	// Submit on Enter
	$('#search_mast').find('input[name="search_location"]').on('keypress', function (e) {
		if (e.which == 13 || e.keyCode == 13) $form.trigger('submit');
	});

	// Autocomplete Fields
	$('#search_mast').find('input.autocomplete').each(function () {
		var $input = $(this), multiple = $input.hasClass('single') ? false : true;
		$input.Autocomplete({
			multiple : multiple,
			params : function () {
				return {
					feed : $form.find('input[name="idx"]').val() || $form.find('input[name="feed"]').val()
				};
			}
		});
	});

	function rgb2hex (rgb) {
	    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
	    function hex(x) {
	        return ("0" + parseInt(x).toString(16)).slice(-2);
	    }
	    return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
	}

	// Calculate Lighter or Darker Hex Colors
	var ColorLuminance = function (hex, lum) {
		// convert rgb to hex
		if (hex.search('rgb') !== -1) {
			hex = rgb2hex(hex);
		}
		// validate hex string
		hex = String(hex).replace(/[^0-9a-f]/gi, '');
		if (hex.length < 6) {
			hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
		}
		lum = lum || 0;
		// convert to decimal and change luminosity
		var rgb = "#", c, i;
		for (i = 0; i < 3; i++) {
			c = parseInt(String(hex).substr(i*2,2), 16);
			c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
			rgb += ("00"+c).substr(c.length);
		}
		return rgb;
	}, useThisColor = $('<div></div>').appendTo('body').addClass('sliderColor').css('background-color');

	// Price Range Values
	var getValues = function ($el) {
		var opts = [], val;
		$el.find('option').each(function () {
			opts.push(this.value);
		});
		return opts;
	};

	// Get Index
	var getIndex = function (arr, val) {
		if (arr.indexOf) {
			return arr.indexOf(val);
		} else {
			var i = 0, l = arr.length, v;
			for (i; i < l; i++) {
				v = arr[i];
				if (v === val) return i	;
			}
		}
		return -1;
	};

	// Build Range Label
	var buildRangeLabel = function (minPrice, maxPrice) {
		// Price Range Label
		if ((minPrice < 1 && maxPrice < 1) || (!minPrice && !maxPrice)) {
			return 'Any Price';
		} else if (!maxPrice || maxPrice < 1) {
			return '$' + parseInt(minPrice).format() + '+';
		} else if (!minPrice || minPrice < 1) {
			return 'Under $' + parseInt(maxPrice).format();
		} else {
			return '$' + parseInt(minPrice).format() + ' - $' + parseInt(maxPrice).format();
		}
	};

	// Sliders
	$.each([
		{ el : '#search_price', min : '#minimum_price', max : '#maximum_price' },
		{ el : '#search_rent', min : '#minimum_rent', max : '#maximum_rent' }
	], function (i, slider) {

		// Slider Configuration
		var $el = $(slider.el)
			, $slider = $el.find('.noUiSlider')
			// Min Price
			, $minPrice = $(slider.min)
			, minPrices = getValues($minPrice)
			, minPrice = getIndex(minPrices, $minPrice.val())
			// Max Price
			, $maxPrice = $(slider.max)
			, maxPrices = getValues($maxPrice)
			, maxPrice = getIndex(maxPrices, $maxPrice.val())
			// Slider Config
			, numSteps = 5
			, maxRange = (maxPrices.length) * numSteps
		;

		// Fix Current Values
		minPrice = minPrice !== -1 ? minPrice : 0;
		maxPrice = maxPrice !== -1 && maxPrice !== 0 ? maxPrice : maxPrices.length;

		// Setup Slider
		$slider.noUiSlider({
			range: [0, maxRange],
			start: [minPrice * numSteps, maxPrice * numSteps],
			step: numSteps,
			slide: function () {
				var $this = $(this),
					range = $this.val(),
					minPrice = minPrices[range[0] / numSteps],
					maxPrice = maxPrices[range[1] / numSteps];

				// Update Label
				$small = $this.closest('.field').find('label small').html('<small>' + buildRangeLabel(minPrice, maxPrice) + '</small>');

				// Update Form
				$(slider.min).val(minPrice);
				$(slider.max).val(maxPrice);

				// Update Colour
				var distance = (range[1] - range[0]) / maxRange;
				$this.find('.noUi-origin').css('background', ColorLuminance(useThisColor, distance));

			}
		})

		// Set Slider Colour
		var distance = (maxPrice - minPrice) / maxRange;
		$slider.find('.noUi-origin').css('background', ColorLuminance(useThisColor, distance));

		// Set Slider Label
		var min = minPrices[minPrice], max = maxPrices[maxPrice];
		$slider.closest('.field').find('label').append('<small>' + buildRangeLabel(min, max) + '</small>');

	});

	// Toggle Sale vs Rental Prices
	$form.find('input[name="search_type"], select[name="search_type"]').on('change', function () {
		var $input = $(this), value = $input.val(), checked = $input.attr('checked') || $input.find('option:selected') ? true : false;

		// Price Ranges
		var $sale = $('#search_price'),
			$rent = $('#search_rent');

	    // Rental Prices
	    if (value in {
	    	'Lease' : true,
			'Rental' : true,
			'Rentals' : true,
			'Residential Lease' : true,
			'Commercial Lease' : true,
			'Residential Rental' : true
		}) {
	        $rent.removeClass('hidden').find('select').prop('disabled', false);
	        $sale.addClass('hidden').find('select').prop('disabled', true);
	        $form.find('input[name="minimum_price"], input[name="maximum_price"]').prop('disabled', true);
	        $form.find('input[name="minimum_rent"], input[name="maximum_rent"]').prop('disabled', false);

	    // Sale Prices
	    } else {
	        $sale.removeClass('hidden').find('select').prop('disabled', false);
	        $rent.addClass('hidden').find('select').prop('disabled', true);
	        $form.find('input[name="minimum_price"], input[name="maximum_price"]').prop('disabled', false);
	        $form.find('input[name="minimum_rent"], input[name="maximum_rent"]').prop('disabled', true);

	    }
	});


})();
/* </script> */