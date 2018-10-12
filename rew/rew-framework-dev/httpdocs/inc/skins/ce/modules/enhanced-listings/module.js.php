$('.enhanced').click(function($e) {
    var feed = $e.target.attributes["data-value"].value;
    BREW.Cookie('enhanced-feed', feed, new Date(new Date().getTime() + (24 * 60 * 60 * 1000)));

    $('.tab--content').addClass('hidden');
    $('.enhanced.-is-current').removeClass('-is-current');
    $('.tab--content--' + feed).removeClass('hidden');
    $(this).addClass('-is-current');
});