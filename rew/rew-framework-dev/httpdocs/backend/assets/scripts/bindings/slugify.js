// Slugify defaults
const defaultOpts = {
    specialChars: '',
    lowercase: true
};

// Escape regexp pattern
const escapeRegExp = function (s) {
    return s.replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&');
};

// Bindings for <input data-slugify>
$('input[data-slugify]').each(function () {
    const $input = $(this);
    const extendOpts = $input.data('slugify') || {};
    const pluginOpts = $.extend({}, defaultOpts, extendOpts);
    const specialChars = escapeRegExp(pluginOpts.specialChars);
    const regexp = new RegExp(`[^a-zA-Z0-9\-${specialChars}]`, 'g');
    $input.on('keyup.slugify blur.slugify', function () {
        const value = $input.val();
        var slug = value.replace(/\s+/g,'-').replace(regexp, '');
        if (pluginOpts.lowercase) slug = slug.toLowerCase();
        $input.data('value', value).val(slug);
    });
});