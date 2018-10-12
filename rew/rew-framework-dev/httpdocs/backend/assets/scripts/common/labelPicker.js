// Define label styles
const LABEL_STYLES = [
    { value: 'red', text: 'Red' },
    { value: 'rose', text: 'Rose' },
    { value: 'violet', text: 'Violet' },
    { value: 'purple', text: 'Purple' },
    { value: 'blue', text: 'Blue' },
    { value: 'azure', text: 'Azure' },
    { value: 'seafoam', text: 'Seafoam' },
    { value: 'lime', text: 'Lime' },
    { value: 'green', text: 'Green' },
    { value: 'orange', text: 'Orange' },
    { value: 'blaze', text: 'Blaze' },
    { value: 'grenadine', text: 'Grenadine' },
    { value: 'bean', text: 'Bean' },
    { value: 'almond', text: 'Almond' },
    { value: 'marigold', text: 'Marigold' },
    { value: 'canary', text: 'Canary' },
    { value: 'yellow', text: 'Yellow' },
    { value: 'grey', text: 'Grey' }
];

// Label style picker
export default (selector) => {
    $(selector).selectize({
        options: LABEL_STYLES,
        preload: true,
        maxItems: 1,
        render: {
            item: (item, escape) => (
                '<div class="token">'
                    + `<span class="token__thumb thumb thumb--tiny -bg-${escape(item.value)}"></span>`
                    + `<span class="token__label">${escape(item.text)}</span>`
                + '</div>'
            ),
            option: (item, escape) => (
                '<div class="token w1/1">'
                    + `<span class="token__thumb thumb thumb--tiny -bg-${escape(item.value)}"></span>`
                    + `<span class="token__label">${escape(item.text)}</span>`
                + '</div>'
            )
        }
    });
};