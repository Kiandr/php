import URLS from 'constants/urls';

// Application search containers
const $results = $('#app_search_results');
const $overlay = $('#overlay');
const SEARCH_DEFER = 300;
const body = document.body;
let searchTimeout = null;

// Handle display of search form
const showSearch = () => {
    $overlay.fadeIn().find('input').focus();
    body.style.overflow = 'hidden'; // Prevent double scroll bars
};

// Handle hiding of search form
const hideSearch = () => {
    $overlay.fadeOut();
    body.removeAttribute('style');
};

// Bind click event to show/hide search links
$('#search-overlay-show').on('click', showSearch);
$('#search-overlay-hide').on('click', hideSearch);

// Hide search form when ESC key is pressed
$(document).on('keyup.search', function (e) {
    if (e.keyCode === 27) hideSearch();
});

// Handle loading search results
$overlay.on('keyup', 'input[name="q"]', function () {

    // 3 character minimum
    const query = this.value;
    if (query.length < 3) return;

    // Display loading state
    $results.html('<p><em>Loading...</em></p>');

    // Defer AJAX request to prevent server hammering
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function () {
        $.ajax({
            url: `${URLS.backendAjax}html.php`,
            dataType: 'html',
            type: 'GET',
            data: {
                module: 'app_search',
                q: query
            }
        })
            .done(html => $results.html(html))
            .fail(() => $results.html(''));
    }, SEARCH_DEFER);

});
