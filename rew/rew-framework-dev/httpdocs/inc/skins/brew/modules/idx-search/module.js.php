/* <script> */
var BREW = BREW || {};
BREW.Modules = BREW.Modules || {};
BREW.Modules.IDX_SEARCH = {
	init : function () {
		'use strict';

		// Refine Form
		$('form.idx-search').each(function () {
			var $form = $(this);

			// Run Once
			if ($form.hasClass('form-ready')) return;
			$form.addClass('form-ready');

			// Toggle Panels
			$form.on(BREW.events.click, '.toggle.field>label, .toggle.field .pair label', function () {
				var $label = $(this)
					, $field = $label.closest('.field')
					, $pair = $field.find('.pair')
				;
				$field = $pair.length > 0 ? $pair : $field;
				if ($field.hasClass('closed')) {
					// Open Panel
					$field.removeClass('closed').find('.details').hide(function () {
						$(this).removeClass('hidden').slideDown('fast');
					});
				} else {
					// Close Panel
					$field.addClass('closed').find('.details').slideUp('fast', function () {
						$(this).addClass('hidden');
					});
				}
				return true;
			});

			// Range Inputs
			$form.find('.range').each(function () {
				var $range = $(this)
					, $min = $range.find('.min select')
					, $max = $range.find('.max select')
				;
				if ($min.length > 0 && $max.length > 0) {
					$min.on('change', function () {
						var min = parseInt($min.val())
							, max = parseInt($max.val())
						;
						if (min > max) $max.val('');
						return true;
					});
					$max.on('change', function () {
						var min = parseInt($min.val())
							, max = parseInt($max.val())
						;
						if (min > max) $min.val('');
						return true;
					});
				}
			});

			// Autocomplete Inputs
			$form.find('input.autocomplete').each(function () {
				var $input = $(this)
					, multiple = !$input.hasClass('single')
				;
				$input.Autocomplete({
					multiple : multiple,
					params : function () {
						return {
							search_city : $.map($form.find('select[name="search_city"], input[name="search_city[]"]:checked'), function (input) {
								return $(input).val();
							}),
							feed : $form.find('input[name="idx"]').val() || $form.find('input[name="feed"]').val()
						};
					}
				});
			});

			// Check Map
			var $bounds = $form.find('input[name="map[bounds]"]:checkbox');
			$form.on('checkMap', function () {

			    // Disable if Polygon Set
			    var $polygon = $form.find('input[name="map[polygon]"]').val();
			    if ($polygon && $polygon.length > 0) {
					return 'Polygon';
			    }

			    // Disable if Radius Set
			    var $radius = $form.find('input[name="map[radius]"]').val();
			    if ($radius && $radius.length > 0) {
					return 'Radius';
			    }

			    // Map Has Active Polygon / Radius Searches
				if (typeof $map != 'undefined') {
					if ($map.REWMap('getPolygons')) return 'Polygon';
					if ($map.REWMap('getRadiuses')) return 'Radius';
				}

			    // Disable if Bounds Checked
			    if ($bounds.attr('checked')) return 'Bounds';

			    // No Map Criteria
			    return false;

			});

			// Location Check
			$form.on('toggleLocations', function () {

				// Location Fields
				var $locations = $form.find(':input.location');

				// Map Search Criteria
				var mapSearch = $form.triggerHandler('checkMap');

			    // Disable if Bounds Checked
			    if (mapSearch) {
					$locations.attr('disabled', 'disabled').addClass('disabled');
			    	return;
			    }

			    // Enable Location Fields
			    $locations.removeAttr('disabled').removeClass('disabled');

			}).trigger('toggleLocations');

			// Search in Bounds
			$form.find('input[name="map[bounds]"]:checkbox').on(BREW.events.click, function () {
				var $this = $(this)
					, checked = $this.attr('checked') ? true : false
					, $tooltip = $this.closest('.details').find('small')
				;
				if (checked) {
					$tooltip.removeClass('hidden');
				} else {
					$tooltip.addClass('hidden');
				}
				// Toggle Locations
				$form.trigger('toggleLocations');
				return true;
			});

			// Check "All Properties" if all types are checked
			var $types = $form.find(':checkbox[name="search_type[]"]');
			if ($types.not('[value=""]').length === $types.filter(':checked').length) {
				$types.prop('checked', true);
			}

			// Property Type Change
			$form.find('input[name^="search_type"], select[name="search_type"]').on('change', function () {
				var $input = $(this)
					, value = $input.val()
					, checked = $input.attr('checked') || $input.find('option:selected').length === 1 ? true : false
				;

				// Check all property types
				var allChecked = false;
				if ($input.is(':checkbox')) {
					var $inputs = $form.find('input[name^="search_type"]');
					if (value === '') {
						$inputs.prop('checked', checked);
						allChecked = checked;
					} else {
						var $checkAll = $inputs.filter('[value=""]')
							, canCheck = $inputs.not($checkAll).length
							, numChecked = $inputs.not($checkAll).filter(':checked').length
							, allChecked = numChecked === canCheck
						;
						$checkAll.prop('checked', allChecked);
					}
					value = $inputs.filter(':checked').map(function () {
						return this.value;
					}).get();
					if (value.length === 1) {
						value = value[0];
					}
				}

				// Price Ranges
			    var $prices = $form.find('#field-price'),
			    	$sale = $prices.find('.sale'),
					$rent = $prices.find('.rent'),
					rentals = ['Rental', 'Rentals', 'Lease', 'Residential Lease', 'Commercial Lease', 'Residential Rental']
			    ;

			    // Show rental prices
			    var rentPrices = false;
			    if (typeof value === 'string') {
					rentPrices = $.inArray(value, rentals) !== -1;
			    } else if (typeof value === 'object' && value.length > 0) {
					rentPrices = value.filter(function (val) {
						return $.inArray(val, rentals) !== -1;
					}).length === value.length;
				}

			    // Rental Prices
			    if (rentPrices) {
			        $rent.removeClass('hidden').find('select').removeAttr('disabled');
			        $sale.addClass('hidden').find('select').attr('disabled', 'disabled');

			    // Sale Prices
			    } else {
			        $sale.removeClass('hidden').find('select').removeAttr('disabled');
			        $rent.addClass('hidden').find('select').attr('disabled', 'disabled');

			    }

				// Update Sub-Types
				var pid = Math.random() * 5, $subtypes = $form.find('select[name="search_subtype"]').data('pid', pid);
			    if ($subtypes.length) {
			        $.ajax({
			            'url' : '/idx/inc/php/ajax/json.php?searchTypes',
			            'type' : 'POST',
			            'dataType' : 'json',
			            'data' : {
			                'pid' : pid,
			                'feed' : $form.find('input[name="idx"]').val() || $form.find('input[name="feed"]').val(),
			                'search_type' : allChecked ? '' : value
			            },
			            'success'  : function (json) {
			                if (!json || json.pid != $subtypes.data('pid')) return;
			                if (json.returnCode == 200) {
			                    var className = $subtypes.attr('class'), html = '<select name="search_subtype"' + (className ? ' class="' + className + '"' : '') + '>';
			                    if (allChecked || typeof value !== 'string') {
				                    if (value.length === 1) {
										html += '<option value="">All ' + value[0] + ' Listings</option>';
				                    } else {
										html += '<option value="">All Properties</option>';
				                    }
			                    } else {
			                        html += '<option value="">All ' + value + ' Listings</option>';
			                    }
			                    if (json.options.length > 0) {
			                        var i = 0, len = json.options.length;
			                        var subtype = $subtypes.val();
			                        while (i < len) {
			                            var option = json.options[i]
											, checked = (subtype == option.value) ? ' selected' : ''
				                        ;
			                            html += '<option value= "' + option.value + '"' + checked + '>' + option.title + '</option>';
			                            i++;
			                        }
			                    }
			                    $subtypes.replaceWith(html);
			                }
			            }
			        });
			    }

			    // Return True
			    return true;

			});

		});
	}
};

BREW.Modules.IDX_SEARCH.init();