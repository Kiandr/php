$(REW).on('idx-refresh', function () {
    var $container = $('.bearer-of-criteria');
    var count = parseInt($container.data('count')) || 0;
    REW.settings.criteria = $container.data('criteria') || {};
    REW.settings.criteria.search_title = $container.data('title') || null;

    var canSave = (count < REW.settings.idx.savedSearchMaxCount);
    var isSaved = $('[name="saved_search_id"]');

    // Show or hide delete search button
    if (isSaved.length) {
        REW.settings.criteria.saved_search_id = isSaved.val();
        $('.delete-search').removeClass('uk-hidden');
    } else {
        $('.delete-search').addClass('uk-hidden');
    }

    // Enable or disable save search button, show or hide over limit warning
    if (canSave) {
        $('.save-search').removeAttr('disabled');
        $('[data-save-over-limit]').addClass('uk-hidden');
    } else {
        $('.save-search').attr('disabled', 'disabled');
        $('[data-save-over-limit]').removeClass('uk-hidden');
    }
});
