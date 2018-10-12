$('.module-ajax').each(function () {
    var $this = $(this);
    var $parent = $this.parent();

    var url = REW.settings.urls.loader;

    var qs = {
        ajaxModule: $this.attr('id')
    };
    qs = $.param(qs);
    url += (url.indexOf('?') == -1 ? '?' : '&') + qs;

    $.get(url, function (data) {
        var $el = $(data);
        $this.replaceWith($el);
        REW.Bind($parent);
    });
});
