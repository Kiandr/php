import showSuccess from 'utils/showSuccess';
import showErrors from 'utils/showErrors';

// Nav pages D&D sorting
$('#nav-pages ul').each(function () {
    const $list = $(this);
    $list.sortable({
        axis: 'y',
        placeholder: 'ui-state-highlight',
        forcePlaceholderSize: true,
        handle: '.nodes__handle',
        cursor: 'move',
        update: function () {
            $.ajax({
                url: '?order',
                type: 'post',
                dataType: 'json',
                data: $list.sortable('serialize')
            }).done(data => {
                if (data && data.ok) {
                    showSuccess(['Navigation has been updated.'], undefined, {
                        close: true
                    });
                }
            }).fail(() => {
                showErrors(['Navigation failed to update.'], undefined, {
                    close: true
                });
            });
        }
    });
});
