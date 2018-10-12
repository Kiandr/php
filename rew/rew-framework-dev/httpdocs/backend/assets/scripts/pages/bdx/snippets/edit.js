// Load BDX snippet scripts
import 'common/bdx_snippets';

// State Select Listener
$('select[name="state"]').on('change', function() {
    $('.panel-City').html('');
    var state = $(this).val();
    var selected;
    if (state) {
        $.getJSON('/builders/php/ajax/json.php?cities', {
            state: $('select[name="state"]').val(),
        }, function (data) {
            var rows   = data.options ? data.options : [];
            var cities = $('#cities').data('value');
            for (var i=0; i < rows.length; i++) {
                selected = (cities.indexOf(rows[i].value) > -1 ? true : false);
                var checked = '';
                if (selected) {
                    checked = 'checked';
                }
                $('.panel-City').append('<label><input type="checkbox" name="search[City][]" value="' + rows[i].value + '"' + checked + '> ' + rows[i].value + '</label>');
            }
        });
        $('#bdx-panels-container').removeClass('hidden');
    } else {
        $('#bdx-panels-container').addClass('hidden');
    }
}).trigger('change');
