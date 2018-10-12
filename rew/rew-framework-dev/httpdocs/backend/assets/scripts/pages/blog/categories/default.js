import showSuccess from 'utils/showSuccess';
import showErrors from 'utils/showErrors';

// Blog categories D&D sorting
$('#blog-categories ul').each(function () {
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
            }).done(() => {
                showSuccess(['Blog categories has been updated.'], undefined, {
                    close: true
                });
            }).fail(() => {
                showErrors(['Blog categories failed to update.'], undefined, {
                    close: true
                });
            });
        }
    });
});
