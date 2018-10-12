$(document).on('click', '#sub-quicksearch .title', function () {
    var $this = $(this);
    var $parent = $this.parents('.field');
    var $details = $parent.find('.details');
    $details.toggleClass('uk-hidden');
    $parent.toggleClass('closed', $details.hasClass('uk-hidden'));
});
