// Toggle Auto-Rotate Settings
$('input[name="auto_rotate"]').on('change', function () {
    const $this = $(this), value = $this.val();
    const enabled = value === 'true' && this.checked;
    $('#auto-rotate-settings').toggleClass('hidden', !enabled);
});

// Toggle Auto-Opt-Out Settings
$('input[name="auto_optout"]').on('change', function () {
    const $this = $(this), value = $this.val();
    const enabled = value === 'true' && this.checked;
    $('#auto-optout-settings').toggleClass('hidden', !enabled);
});

// Toggle Mail Settings
$('input[name="mail_provider"]').on('change', function () {
    const $this = $(this)
        , value = $this.val()
        , checked = this.checked
        , $settings_mandrill = $('#mail-mandrill-settings')
        , $settings_sendgrid = $('#mail-sendgrid-settings')
        ;
    if (value === 'mandrill' && checked) {
        $settings_mandrill.removeClass('hidden');
        $settings_sendgrid.addClass('hidden');
    } else if (value === 'sendgrid' && checked) {
        $settings_sendgrid.removeClass('hidden');
        $settings_mandrill.addClass('hidden');
    } else {
        $settings_mandrill.addClass('hidden');
        $settings_sendgrid.addClass('hidden');
    }
});

// Update Time Slider
const updateTimeSlider = ($input, $label, ui) => {
    var min = ui.values[0], max = ui.values[1];
    var A = [], a = min, b = (max > 23) ? 23 : max;
    A[0] = a;
    while (a + 1 <= b){
        A[A.length]= a+= 1;
    }
    $input.val(A.join(','));
    if (min == 0) {
        min = '12:00am';
    } else if (min < 12) {
        min = min + ':00am';
    } else {
        min = ((min - 12) == 0 ? 12 : (min - 12)) + ':00pm';
    }
    if (max == 0) {
        max = '12:59am';
    } else if (max < 12) {
        max = max + ':59am';
    } else {
        max = ((max - 12) == 0 ? 12 : (max - 12)) + ':59pm';
    }
    if (min == max) {
        $label.text(min);
    } else {
        $label.text(min + '-' + max);
    }
};

// UI Range Sliders
$('div.slider').each(function () {
    const $this = $(this).hide();
    const $input = $this.find('input');
    const $label = $('<span style="margin-left: 12px"></span>').appendTo($this.siblings('label'));
    const min = Math.min.apply(Math, $input.val().split(','));
    const max = Math.max.apply(Math, $input.val().split(','));
    const slider = $('<div></div>').insertAfter($this).slider({
        range: true,
        min: 0,
        max: 23,
        values: [min, max],
        slide: function (event, ui) {
            updateTimeSlider($input, $label, ui);
        },
        change: function (event, ui) {
            updateTimeSlider($input, $label, ui);
        }
    });
    updateTimeSlider($input, $label, {
        values: slider.slider('values')
    });
});

// Price Range Toggle
const $prices = $('#field-price');
$('input[name="scoring[rental]"]').on('change', function () {
    const rentals = this.value === 'yes' && this.checked;
    $prices.find('.sale').toggleClass('hidden', rentals).find('select').prop('disabled', rentals);
    $prices.find('.rent').toggleClass('hidden', !rentals).find('select').prop('disabled', !rentals);
}).filter(':checked').trigger('change');