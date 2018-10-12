// Remove Agent Feature
$('a.manage_image').on('click', function () {
    if (confirm('Are you sure you want to remove this photo?')) {
        $(this).remove();
        alert('Photo has been removed. Be sure to save your changes.');
    }
});
