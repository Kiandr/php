import 'autonumeric';

// autoNumeric defaults
const defaultOpts = {
    aSign: '$',
    lZero: 'deny',
    wEmpty: 'sign',
    vMax: '999999999',
    vMin: 0,
    mDec: 0
};

// Bindings for <input data-numeric>
$('input[data-currency]').each(function () {
    const $input = $(this);
    const extendOpts = $input.data('currency') || {};
    const pluginOpts = $.extend({}, defaultOpts, extendOpts);
    $input.autoNumeric('init', pluginOpts);
});