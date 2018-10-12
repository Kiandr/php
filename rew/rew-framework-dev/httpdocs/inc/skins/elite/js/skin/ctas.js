$('.toggle-container .toggle').on('click', function () {
    var $this = $(this);
    var $parent = $this.parents('.toggle-container');

    $parent.toggleClass('inactive');
    var active = !$parent.hasClass('inactive');
    $parent.toggleClass('active', active);
    $parent.find('.uk-icon-close, .uk-icon-open').toggleClass('uk-icon-close', active).toggleClass('uk-icon-open', !active);
});
