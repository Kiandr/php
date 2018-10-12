/**
 * Render settings
 * @type {Object}
 */
export default {
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
};
