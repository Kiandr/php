import showSuccess from 'utils/showSuccess';
import showErrors from 'utils/showErrors';

// Communities D&D sorting
$('#communities ul').sortable({
    axis: 'y',
    placeholder: 'ui-state-highlight',
    forcePlaceholderSize: true,
    handle: '.nodes__handle',
    cursor: 'move',
    update: function () {
        $.ajax({
            url: '?' + $(this).sortable('serialize'),
            type: 'post',
            dataType: 'json',
            data: {
                ajax: true,
                order: true
            }
        }).done(() => {
            showSuccess(['Communities has been updated.'], undefined, {
                close: true
            });
        }).fail(() => {
            showErrors(['Communities failed to update.'], undefined, {
                close: true
            });
        });
    }
});
