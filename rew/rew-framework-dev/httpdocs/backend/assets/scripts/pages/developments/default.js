import showErrors from 'utils/showErrors';
import showSuccess from 'utils/showSuccess';

// Enable sortable developments
$('#developments-list tbody').sortable({
    handle: '.handle',
    cursor: 'move',
    forceHelperSize: true,
    update: function() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: $(this).sortable('serialize')
        }).then(function (data) {
            var error = data && data.error;
            if (!error) showSuccess(['Your changes have been saved.']);
            if (error) showErrors([data.error]);
        }).fail(function () {
            showErrors(['Your changes could not be saved.']);
        });
    },
    helper: function(e, tr) {
        var $originals = tr.children();
        var $helper = tr.clone();
        $helper.children().each(function(index) {
            $(this).width($originals.eq(index).width());
        });
        return $helper;
    }
});