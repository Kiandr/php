export default (el, onChange) => {
    const $input = $(el);
    const $parent = $input.parent();
    $input.autocomplete({
        appendTo: $parent,
        source: function (request, response) {
            const $el = this.element;
            const $form = $el.closest('form');
            const $feed = $form.find('select[name="feed"]');
            const feed = $feed.length ? $feed.val() : '';
            $.getJSON('/idx/inc/php/ajax/json.php?limit=15&search=search_listing', {
                q: request.term,
                feed: feed,
                cache: false
            }, function (data) {
                var parsed = [], rows = data.options ? data.options : [];
                var regex = new RegExp('(?![^&;]+;)(?!<[^<>]*)(' + request.term.replace(/([\^\$\(\)\[\]\{\}\*\.\+\?\|\\])/gi, '\\$1') + ')(?![^<>]*>)(?![^&;]+;)', 'gi');
                for (var i = 0; i < rows.length; i++) {
                    var opt = rows[i];
                    opt.image = opt.image ? opt.image.replace('http://', '/thumbs/60x60/') : '/thumbs/60x60/img/404.gif';
                    opt.title = opt.title.replace(regex, '<mark>$1</mark>');
                    parsed.push(opt);
                }
                response(parsed);
            });
        },
        focus: function (event, ui) {
            $input.val(ui.item.label);
            if (typeof onChange === 'function') {
                onChange(ui.item.label);
            }
            return false;
        }
    }).autocomplete('instance')._renderItem = function (ul, item) {
        var $item = $('<li class="listing">').data('item.autocomplete', item);
        $item.append('<a>'
            + '<img src="' + item.image + '" border="0">'
            + '<span class="bd"><strong class="v">' + item.title + '</strong>'
            + (item.lines ? '<span class="v">' + item.lines.join('</span><span class="v">') + '</span></span>' : '')
            + '</a>');
        return $item.appendTo(ul);
    };
};
