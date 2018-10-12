import URLS from 'constants/urls';

// Get timeline data from DOM element
const $history = $('#app__history');
if ($history.length === 1) {

    // Request data
    var data = {
        current_page: $history.data('timeline-page'),
        timeline_mode: $history.data('timeline-mode'),
        timeline_id: sessionStorage.getItem('timeline_id')
    };

    // Go to last page request
    $.ajax({
        url: `${URLS.backendAjax}history.php`,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function (data) {
            if (data && data.timeline_id) {
                const timeline_id = data.timeline_id;
                sessionStorage.setItem('timeline_id', timeline_id);
                if (typeof data.last_page === 'string') {
                    $('a.timeline__back').each(function () {
                        $(this).attr('href', data.last_page);
                    });
                }
            }
        }
    });

}