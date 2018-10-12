(function() {
    if (!REW.settings.idx.subTypes) return;

    $(document).on('change', '#field-type input[type="checkbox"]', function () {
        if (($(this).val() == '')) {
            if ($(this).prop('checked')) {
                $('#field-type input[type="checkbox"]').prop('checked', true);
            }else{
                $('#field-type input[type="checkbox"]').prop('checked', false);
            }
        }
        var checked = {};
        $('#field-type input[type="checkbox"]:checked').each(function () {
            var v = $(this).val();
            if (v == '') { //  All Properties selected
                checked = {};
                return false;
            }
            checked[v] = true;
        });

        var $el = $('#field-subtype select');
        var $all = (Object.keys(checked).length === 1 ? $('<option></option>').attr('value', '').text('All ' + Object.keys(checked)[0] + ' Listings') : $('<option></option>').attr('value', '').text('All Properties'));
        $el.empty();
        $el.append($all);
        var shown = {};

        $.each(REW.settings.idx.subTypes, function (key, value) {
            if (checked[key] || jQuery.isEmptyObject(checked)) {
                $.each(value, function (k, v) {
                    if (!shown[v.value]) {
                        shown[v.value] = true;
                        $el.append($('<option></option>').attr('value', v.value).text(v.title));
                    }
                });
            }
        });
    });
})();
