// Autocomplete Fields
$('#bdx-builder-form').find('input.bdx-autocomplete').each(function (i, el) {

    el = $(el);
    el.autocomplete({
        source: function (request, response) {

            $.getJSON('/builders/php/ajax/json.php', {
                q : request.term,
                state: $('select[name="state"]').val(),
                search: el.attr('name')
            }, function (data) {
                var parsed = [];
                var rows   = data.options ? data.options : [];
                for (var i=0; i < rows.length; i++) {
                    var row = $.trim(rows[i].title);
                    if (row) {
                        row = row.split('|');
                        parsed.push({
                            value: row[0],
                            label: row[0]
                        });
                    }
                }
                response(parsed);
            });

        },
        focus: function () {
            return false;
        }
    });
});

// Snippet Mode Listener
$('input[name="snippet_mode"]').change(function() {
    var mode = $(this).val();
    if (mode == 'homes') {
        $('.community-sort').addClass('hidden').attr('disabled', 'disabled');
        $('.home-sort').removeClass('hidden').removeAttr('disabled');
    } else {
        $('.home-sort').addClass('hidden').attr('disabled', 'disabled');
        $('.community-sort').removeClass('hidden').removeAttr('disabled');
    }
    $('select[name="search[sort_by]"] option:not([disabled])').first().attr('selected', 'selected');
});
