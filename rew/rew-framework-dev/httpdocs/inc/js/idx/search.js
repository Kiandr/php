// Refine Bar, Property Type
$('input.idx-search-type, input[name^="search_type"], select[name="search_type"]').on('change', function () {
    var $input = $(this)
        , value = $input.val()
        , checked = $input.prop('checked') || $input.find('option:selected').length === 1 ? true : false
 ;

    // Check all property types
    var allChecked = false;
    if ($input.is(':checkbox')) {
        var $inputs = $('input[name^="search_type"]');
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
    var $prices = $('#field-price'),
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
    var pid = Math.random() * 5, $subtypes = $('select[name="search_subtype"]').data('pid', pid);
    if ($subtypes.length) {
	    $.ajax({
	        'url' : '/idx/inc/php/ajax/json.php?searchTypes',
	        'type' : 'POST',
	        'dataType' : 'json',
	        'data' : {
	            'pid' : pid,
	            'feed' : $('input[name="idx"]').val() || $('input[name="feed"]').val(),
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

// Check "All Properties" if all types are checke
var $types = $(':checkbox[name="search_type[]"]');
if ($types.not('[value=""]').length === $types.filter(':checked').length) {
    $types.prop('checked', true);
}