/* <script> */
(function () {
	'use strict';

	// @link http://stackoverflow.com/a/20056485/3597033
	$(document).on('DOMNodeInserted', '.pac-container', function () {
		$(this).find('.pac-item, .pac-item span').addClass('needsclick');
	});

	// Featured module content
	var $module = $('#<?=$this->getUID(); ?>')
		, $search = $('#find-a-home')
		, $sell = $('#sell-my-home')
		, idx_feed = ''
		, gautocomplete
	;

	// Toggle buy/sell CTA
	$module.on('click', 'a[data-target]', function () {
		var $link = $(this), target = $link.data('target'), $target = $(target);
		$target.removeClass('hidden').siblings('form').addClass('hidden');
		$link.parent().addClass('current').siblings().removeClass('current');
		if (target === '#sell-my-home' && typeof loadSellCTA === 'function') loadSellCTA();
		return false;
	});

	// CMA seller feature
	if ($sell.length === 1) {
		var $input = $sell.find('input[name="adr"]');

		// Send visitor to CMA form
		var goToCMA = function (adr) {
			var redirect = '/cma.php'
				, params = []
			;
			if (idx_feed.length > 0) {
				params.push('feed=' + encodeURIComponent(idx_feed));
			}
			if (typeof adr === 'string') {
				params.push('adr=' + encodeURIComponent(adr));
			}
			window.location.href = redirect + (params.length > 0 ? '?' + params.join('&') : '');
		};

		// Components to look for
		var expectedComponents = {
			street_number: 'long_name',
			premise: 'long_name',
			route: 'long_name',
			locality: 'long_name',
			administrative_area_level_1: 'short_name',
			administrative_area_level_2: 'long_name',
			postal_code: 'long_name'
		};

		// Get address components for Google Places Autocomplete
		var getAddressComponents = function () {

			// Require google place result
			var place = gautocomplete.getPlace();
			if (!place) return false;

			// Require address components
			var adr = { full_address: place.formatted_address || place.name }
				, adr_comps = place.address_components
			;
			if (!adr_comps) return adr;

			// Return expected components
			var l = adr_comps.length, i = 0;
			for (i; i < l; i++) {
				var adr_comp = adr_comps[i]
					, adr_type = adr_comp.types[0]
				;
				if (expectedComponents[adr_type]) {
					adr[adr_type] = adr_comp[expectedComponents[adr_type]];
				}
			}
			return adr;

		};

		// Load "Sell my Home" CTA
		var loadSellCTA = function () {

			// Google Places Autocomplete
			REWMap.libraries.push('places');
			REWMap.loadApi(function () {

				// Google places autocomplete
				gautocomplete = new google.maps.places.Autocomplete($input.get(0), {
					'types' : ['geocode'],
					'componentRestrictions' : {
						'country' : '<?=Settings::getInstance()->LANG === 'en-CA' ? 'CA' : 'US'; ?>'
					}
				});

			});

			// CMA form handler
			var processing = false;
			$sell.on('submit', function () {

				// Currently processing...
				if (processing) return false;
				processing = true;

				// Address components
				var adr = getAddressComponents();
				if (!adr) {
					adr = { full_address: $input.val() };
				}

				// Street address
				var address = [adr.street_number, adr.route].join(' ').trim();
				if (adr.premise) address = adr.premise + (address ? ', ' + address : '');

				// Send data
				$.ajax({
					url: '/idx/inc/php/ajax/html.php?module=<?=$this->getID(); ?>&options[ajax]=true',
					type: 'POST',
					dataType: 'json',
					data: {
						mi0moecs: $sell.find('input[name="mi0moecs"]').val(),
						email: $sell.find('input[name="email"]').val(),
						full_address: adr.full_address,
						address: address,
						city: adr.locality,
						state: adr.administrative_area_level_1,
						county: adr.administrative_area_level_2,
						zip: adr.postal_code
					}

				// All is well!
				}).done(function (data, textStatus, jqXHR) {
					processing = false;
					goToCMA(adr.full_address);

				// Request failure
				}).fail(function (jqXHR, textStatus, errorThrown) {
					processing = false;
					if (jqXHR.status === 0 || jqXHR.readyState === 0 || errorThrown === 'abort') return;
					goToCMA(adr.full_address);

				});

				// Don't submit
				return false;

			});

		};

	}

	// Search location autocomplete
	var $gallery = $('.gallery');
	$module.find('input[name="search_location"]').Autocomplete({
		multiple: true,
		params: function () {
			return {
				feed: $module.find('input[name="feed"]').val()
			};
		}
	}).on('focus', function () {
		if ($gallery.is('.init')) {
			$gallery.Gallery('stop');
		}
	}).on('blur', function () {
		if ($gallery.is('.init')) {
			$gallery.Gallery('play');
		}
	});

	// Dynamic feed switcher
	$module.on('click', 'a[data-feed]', function () {
		var $link = $(this), feed = $link.data('feed');
		$link.parent().addClass('current')
			.siblings().removeClass('current');
		$search.attr('action', feed.link);
		$module.find('input[name="feed"]').val(feed.name);
		$module.find('input.autocomplete').Autocomplete('refresh');
		idx_feed = feed.name;
		return false;
	});

})();
/* </script> */