require(['nouislider', 'util'], function() {
    $(function() {

        // Add page specific class to body tag
        $('body').addClass('builder-cities');

        // Tabs
        $('.bdx-tabcontain li').click(function(e) {
            e.preventDefault();
            $('.bdx-tabcontain li').removeClass('selected');
            $(this).addClass('selected');
            $('.' + $(this).attr('id') + '-container').removeClass('hidden').siblings().addClass('hidden');
        });

        function rgb2hex (rgb) {
		    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
		    function hex(x) {
		        return ('0' + parseInt(x).toString(16)).slice(-2);
		    }
		    if (rgb) {
		  		return '#' + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
		    }
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
                var rgb = '#', c, i;
                for (i = 0; i < 3; i++) {
                    c = parseInt(String(hex).substr(i*2,2), 16);
                    c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
                    rgb += ('00'+c).substr(c.length);
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
            { el : '.search_price1', min : '.minimum_price1', max : '.maximum_price1' },
            { el : '.search_price2', min : '.minimum_price2', max : '.maximum_price2' }
        ], function (i, slider) {

            // Slider Configuration
            var $el = $(slider.el)
                , $slider = $el.find('.bdx-noUiSlider')
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
                    $small = $this.siblings('.bdx-slider-header').find('small').html('<small>' + buildRangeLabel(minPrice, maxPrice) + '</small>');

                    // Update Form
                    $(slider.min).val(minPrice);
                    $(slider.max).val(maxPrice);

                    // Update Colour
                    var distance = (range[1] - range[0]) / maxRange;
                    $this.find('.bdx-noUi-origin').css('background', ColorLuminance(useThisColor, distance));

                }
            });

            // Set Slider Colour
            var distance = (maxPrice - minPrice) / maxRange;
            $slider.find('.bdx-noUi-origin').css('background', ColorLuminance(useThisColor, distance));

            // Set Slider Label
            var min = minPrices[minPrice], max = maxPrices[maxPrice];
            $slider.closest('.field').find('label').append('<small>' + buildRangeLabel(min, max) + '</small>');

        });
    });
});