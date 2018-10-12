// D&D sorting for agents
const $list = $('#sortable').sortable({
    forceHelperSize: true,
    handle: '.handle',
    cursor: 'move',
    update: function() {
        $.ajax({
            url: '?' + $list.sortable('serialize'),
            type: 'post',
            dataType: 'json',
            data: $.extend({
                ajax: true,
                order: true
            }, $list.data('params'))
        });
    }
});