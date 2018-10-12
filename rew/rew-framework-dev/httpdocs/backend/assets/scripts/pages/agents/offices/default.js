import showSuccess from 'utils/showSuccess';
import showErrors from 'utils/showErrors';

// Offices D&D sorting
$('#offices').sortable({
    axis: 'y',
    placeholder: 'ui-state-highlight',
    forcePlaceholderSize: true,
    handle: '.nodes__handle',
    cursor: 'move',
    update: function () {
        $.ajax({
            url: '?move',
            type: 'post',
            dataType: 'json',
            data: $(this).sortable('serialize')
        }).done(() => {
            showSuccess(['Offices has been updated.'], undefined, {
                close: true
            });
        }).fail(() => {
            showErrors(['Offices failed to update.'], undefined, {
                close: true
            });
        });
    }
});
