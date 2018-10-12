var $parent = $('.header-search');
var fields = global.REW.price_fields = {
    min_price: '[name=minimum_price]',
    max_price: '[name=maximum_price]',
    min_rent: '[name=minimum_rent]',
    max_rent: '[name=maximum_rent]'
};

if ($parent.length > 0) {
    var $priceContainer = $parent.find('.idx-price');
    var lastType = '';

    if ($priceContainer.length > 0) {
        // Display editor on click
        $priceContainer.find('.label').first().on('click', '',function (event) {
            var $wrapper = $priceContainer.find('.wrapper');
            //if ($.contains($wrapper[0],event.target)) return false;
            $wrapper.toggleClass('uk-hidden');
            $priceContainer.find('.label').toggleClass('open', !$wrapper.hasClass('uk-hidden'));
        });

        function unifyNumber (number) {
            if (!number) return '';

            if (number >= 1000000000) {
                return Math.ceil(number / 1000000) / 1000 + 'B';
            } else if (number >= 1000000) {
                return Math.ceil(number / 1000) / 1000 + 'M';
            } else if (number >= 1000) {
                return Math.ceil(number / 1000) + 'K';
            } else {
                return number;
            }
        }

        function setLabel (type) {
            if (typeof(type) == 'undefined') {
                type = lastType;
            } else {
                lastType = type;
            }

            // Use the visible element if possible. Otherwise (i.e. during load) use the first
            var $min = $(fields['min_' + type] + ':visible');
            var $max = $(fields['max_' + type] + ':visible');
            if (!$min.length) {
                $min = $(fields['min_' + type]);
            }
            if (!$max.length) {
                $max = $(fields['max_' + type]);
            }
            var min = unifyNumber($min.val());
            var max = unifyNumber($max.val());

            var text;
            var $label = $priceContainer.find('.label');

            if (min && max) {
                text = '$' + min + ' - $' + max;
            } else if (min) {
                text = '$' + min + ' - Max';
            } else if (max) {
                text = 'Min - $' + max;
            } else {
                text = $label.data('placeholder');
            }
            $label.text(text);
        }

        // Find all the fields we need
        var fieldSet = Object.keys(fields).map(function (key) {
            return fields[key];
        }).join(', ');

        $(document).on('change', fieldSet, function () {
            var $this = $(this);
            var name = $this.attr('name');

            // Make sure we set any hidden elements. This can be in other forms.
            $('[name="' + name + '"]').val($this.val());
            setLabel($(this).attr('id').substring(-5) == '_rent' ? 'rent' : 'price');
        });
        setLabel('price');

        REW.SetPriceLabel = setLabel;
    }
}
