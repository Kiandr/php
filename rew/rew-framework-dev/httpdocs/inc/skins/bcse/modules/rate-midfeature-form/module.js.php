/* <script> */
(function () {
	'use strict';

	// Search Form
	var $form = $('#<?=$this->getUID() ; ?>');
	var place, address, autocomplete;
	var componentsUsed = {
		street_number: 'long_name',
		route: 'long_name',
		locality: 'long_name',
		administrative_area_level_1: 'short_name',
		administrative_area_level_2: 'long_name',
		country: 'long_name',
		postal_code: 'long_name'
	};


	// Google Places Auto-Complete for CMA Feature
	REWMap.libraries.push('places');
	REWMap.loadApi(function () {
		var $input = $form.find('.gs-address'), input = $input.get(0);
		autocomplete = new google.maps.places.Autocomplete(input, {
			'types' : ['geocode'],
			'componentRestrictions' : {
				'country' : '<?=Settings::getInstance()->LANG === 'en-CA' ? 'CA' : 'US'; ?>'
			}
		});

		// Place selected
		google.maps.event.addListener(autocomplete, 'place_changed', function () {
			place = autocomplete.getPlace() || {};
			address = place.address_components;
		});

		// Location Check
		$form.on('submit', function (e) {
			e.preventDefault();

			//Break Autocomplete Place into usable components
			var componentsProvided = new Array()
			if (!(address === undefined || address.length == 0)) {
				for (var i =0; i<address.length; i++) {
					var address_component = address[i];
					var addressType = address_component.types[0];
					if (componentsUsed[addressType]) {
						componentsProvided[addressType] = address_component[componentsUsed[addressType]];
					}
				}
				if ((componentsProvided['street_number'] !== undefined && componentsProvided['street_number'].length>0) || (componentsProvided['route'] !== undefined && componentsProvided['route'].length>0)) {
					componentsProvided['address'] = componentsProvided['street_number']+' '+componentsProvided['route'];
				}
			}

			// Submit Guaranteed Sold form
			$.ajax({
				'url'		: '/idx/inc/php/ajax/json.php?module=rate-midfeature-form&ajax&apply',
				'type'		: 'POST',
				'data'		: {
					'email'         : $(this).find('input[name="email"]').val(),
					'mi0moecs'      : $(this).find('input[name="mi0moecs"]').val(),
					'full_address'  : $input.val(),
					'street_address': componentsProvided['address'],
					'city'          : componentsProvided['locality'],
					'state'         : componentsProvided['administrative_area_level_1'],
					'county'        : componentsProvided['administrative_area_level_2'],
					'zip'           : componentsProvided['postal_code']
				},
				'success'	: function (json) {
					if (json['success'] == true) {
						$.Window({
							'width'		: 800,
							'height'	: 800,
							'title'		: 'Apply for our Guaranteed Sold Program',
							'content'	: '<div class="gs-popup-container popup-container">' + json['form'] + '</div>',
							'onOpen'	: function (win) {
								var $el = win.getWindow();
								$el.on('submit', 'form', function () {
									$.ajax({
										'url'		: '/idx/inc/php/ajax/json.php?module=rate-midfeature-form&ajax&submit',
										'type'		: 'POST',
										'data'		: $(this).serialize(),
										'success'	: function (json) {
											if (json['form'].indexOf('msg success') !== -1) {
												win.setContents('<div class="gs-popup-container popup-container"><div class="msg success form_snippet"><h5 class="title">Thanks for your interest!</h5><p>We\'ll get back to you as soon as possible.</p></div>');
												// Successful form submission (this is not the best method of detection - but it gets the job done)
												setTimeout(function () {
													win.close();
												}, 1000);
											} else {
												win.setContents('<div class="gs-popup-container popup-container">' + json['form'] + '</div>');
											}
										}
									});
									return false;
								});
							}
						});
					}
				}
			});
		});
	});
})();
/* </script> */
