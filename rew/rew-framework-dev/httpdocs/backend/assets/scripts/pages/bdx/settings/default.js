// Handle select/unselect all checkbox
$('.select_all_toggle').click(function() {
    if ($(this).hasClass('toggle_off')) {
        $('.toggleable').attr('checked', 'checked');
        $(this).removeClass('toggle_off');
        $(this).addClass('toggle_on');
        $(this).text('Remove All');
    } else {
        $('.toggleable').removeAttr('checked');
        $(this).removeClass('toggle_on');
        $(this).addClass('toggle_off');
        $(this).text('Select All');
    }
});
