// Slideshow images D&D sorting
$('#slideshow_images').sortable({
    items: 'li',
    cursor: 'move',
    update: function () {
        $.ajax({
            url: '?updateOrder',
            type: 'get',
            dataType: 'json',
            data: $(this).sortable('serialize')
        });
    }
});