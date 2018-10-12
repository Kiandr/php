import showSuccess from 'utils/showSuccess';
import showErrors from 'utils/showErrors';

// Blog links D&D sorting
$('#blog-links').sortable({
    axis: 'y',
    containment: 'parent',
    placeholder: 'ui-state-highlight',
    forcePlaceholderSize: true,
    handle: '.nodes__handle',
    cursor: 'move',
    update: function () {
        $.ajax({
            url: '?order',
            type: 'post',
            dataType: 'json',
            data: $(this).sortable('serialize')
        }).done(() => {
            showSuccess(['Blog links has been updated.'], undefined, {
                close: true
            });
        }).fail(() => {
            showErrors(['Blog links failed to update.'], undefined, {
                close: true
            });
        });
    }
});
