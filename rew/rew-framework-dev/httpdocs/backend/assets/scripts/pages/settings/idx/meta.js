// Listing link preview
const $placeholder = $('#link-placeholder[data-placeholder]');
const placeholder = $placeholder.data('placeholder');
$('#listing-link').on('keyup.slugify', function () {
    const value = this.value || this.placeholder;
    $placeholder.val(placeholder.replace('{$value$}', value));
}).trigger('keyup.slugify');