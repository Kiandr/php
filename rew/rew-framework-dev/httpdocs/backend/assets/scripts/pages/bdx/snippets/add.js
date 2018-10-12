// Load BDX snippet scripts
import 'common/bdx_snippets';

// State Select Listener
$('select[name="state"]').on('change', function() {
    $('.panel-City').html('');
    var state = $(this).val();
    if (state) {
        $.getJSON('/builders/php/ajax/json.php?cities', {
            state: $('select[name="state"]').val(),
        }, function (data) {
            var rows   = data.options ? data.options : [];
            for (var i=0; i < rows.length; i++) {
                $('.panel-City').append('<label><input type="checkbox" name="search[City][]" value="' + rows[i].value + '"> ' + rows[i].value + '</label>');
            }
        });
        $('#bdx-panels-container').removeClass('hidden');
    } else {
        $('#bdx-panels-container').addClass('hidden');
    }
});

// Trigger Snippet Mode Change
$('input[name="snippet_mode"]').trigger('change');
