const $addCCbutton = $('.add-cc');

$addCCbutton.on('click', function() {
    $(this).closest('div').find('.input').toggleClass('hidden');
    $(this).toggleClass('hidden');
});