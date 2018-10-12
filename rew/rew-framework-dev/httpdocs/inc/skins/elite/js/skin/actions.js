require('../idx/actions');

// Save listing to Favorites
$(document).on('click', '.action-save[data-save]', function () {
    var $this = $(this)
        , $icon = $this.find('i')
        , $text = $this.find('span')
        , data = $this.data('save')
        , $listing = $this.parents('.idx-listings, .dashboard-idx-listings');
        
    IDX.Favorite({
        mls: data.mls,
        feed: data.feed,
        listing: $listing,
        onComplete: function (response) {
            if (response.added) {
                $text.text(data.remove);
            }
            if (response.removed) {
                $text.text(data.add);
            }
        }
    }, $icon);
    return false;
});

// Dismiss listing from results
$(document).on('click', '.action-dismiss[data-dismiss]', function () {
    var $this = $(this)
        , $icon = $this.find('i')
        , $text = $this.find('span')
        , data = $this.data('dismiss')
        , $listing = $this.parents('.idx-listings, .dashboard-idx-listings')
        ;
    IDX.Dismiss({
        mls: data.mls,
        feed: data.feed,
        listing: $listing,
        onComplete: function (response) {
            if (response.added) {
                $text.text(data.remove);
            }
            if (response.removed) {
                $text.text(data.add);
            }
        }
    }, $icon);
    return false;
});

if (REW.listing) {
    IDX.Paginate($.extend(REW.listing, {
        done: function (data) {
            if (!data) return;

            if (data.prev){
                $('.fw-prev-listing').attr('href', data.prev).removeClass('uk-hidden');
            }
            if (data.next) {
                $('.fw-next-listing').attr('href', data.next).removeClass('uk-hidden');
            }
        }
    }));
}
