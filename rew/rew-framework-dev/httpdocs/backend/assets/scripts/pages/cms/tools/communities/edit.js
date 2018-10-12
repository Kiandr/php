import 'selectize';
import handleCharacterCount from '../../../../utils/characterCount';

// IDX builder panels
import 'common/idx-builder';

// Featured community tags
$('select[name="tags[]"]').selectize({
    createOnBlur: true,
    placeholder: '',
    create: true,
    plugins: [
        { name: 'remove_button' },
        { name: 'drag_drop' }
    ]
});

// Toggle search criteria vs IDX snippet for search results
$('input[name="search_criteria"]').on('click', function () {
    const showCriteria = this.value === 'true';
    $('#idx_snippet_panel').toggleClass('hidden', showCriteria);
    $('#search_criteria_panel').toggleClass('hidden', !showCriteria);
});

// Character count for subtitle input
handleCharacterCount('subtitle', 'subtitleCount', 'subtitleMax', 100);