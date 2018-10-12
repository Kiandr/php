// Blog categories D&D sorting
$('#categories ul').each(function () {
    const $list = $(this);
    $list.sortable({
        placeholder: 'ui-state-highlight',
        containment: '#categories',
        forcePlaceholderSize: true,
        handle: '.icon',
        cursor: 'move',
        update: function () {
            $.ajax({
                url: '?order',
                type: 'post',
                dataType: 'json',
                data: $list.sortable('serialize')
            });
        }
    });
});