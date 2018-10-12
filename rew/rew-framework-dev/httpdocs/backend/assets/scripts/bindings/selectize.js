import 'selectize';

// Selectize defaults
const defaultOpts = {plugins: ['remove_button'], maxOptions:10000};

// Initialize selectize inputs
$('select[data-selectize]').each(function () {
    const $select = $(this);
    const extendOpts = $select.data('selectize') || {};
    var renderOpts = {};
    if($select.attr('name') == 'groups[]') {
        renderOpts = {
            render: {
                item: (item, escape) => (
                    '<div class="token">'
                    + `<span class="token__thumb thumb thumb--tiny -bg-${escape(item.style)}"></span>`
                    + `<span class="token__label">${escape(item.text)}</span>`
                    + '</div>'
                ),
                option: (item, escape) => (
                    '<div class="token w1/1">'
                    + `<span class="token__thumb thumb thumb--tiny -bg-${escape(item.style)}"></span>`
                    + `<span class="token__label">${escape(item.text)}</span>`
                    + '</div>'
                )
            }
        };
    }
    const pluginOpts = $.extend({}, defaultOpts, extendOpts, renderOpts);
    $select.selectize(pluginOpts);
});