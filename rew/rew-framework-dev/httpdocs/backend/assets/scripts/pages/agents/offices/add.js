// Remove uploaded office photo
$('a.remove_photo').on('click', function () {
    if (confirm('Are you sure you want to remove this photo?')) {
        $(this).parent().remove();
    }
});
