(function () {
    'use strict';

    // Save favorite
    $('#page').on('click', '.listing a[data-save]', function () {
        var $link = $(this)
            , $listing = $link.closest('.listing')
            , listing = $listing.data('listing')
  ;
        $listing.Favorite({
            mls: listing.mls,
            feed: listing.feed,
            onComplete: function (response) {
                if (response.added) {
                    $link.attr('title', 'Remove Saved Property');
                } else if (response.removed) {
                    $link.attr('title', 'Save this Property');
                } else if (response.error) {
                    //console.log('error:', response.error);
                }
            }
        });
    });

    // Dismiss listing
    $('#page').on('click', '.listing a[data-hide]', function () {
        var $link = $(this)
            , $listing = $link.closest('.listing')
            , listing = $listing.data('listing')
  ;
        $listing.Dismiss({
            mls: listing.mls,
            feed: listing.feed,
            onComplete: function (response) {
                if (response.added) {
                    $link.attr('title', 'Show this Property');
                } else if (response.removed) {
                    $link.attr('title', 'Hide this Property');
                } else if (response.error) {
                    //console.log('error:', response.error);
                }
            }
        });
    });

})();