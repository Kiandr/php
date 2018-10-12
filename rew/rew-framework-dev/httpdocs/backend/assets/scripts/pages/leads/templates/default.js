// View document preview
$('#templates-list').on('click', 'a.view', function () {
    var popup = window.open(this.href + '&popup=true', 'templateview', 'height=500,width=850,scrollbars=1,location=no,toolbar=no,resizable=yes');
    if (popup) popup.focus();
    return false;
});