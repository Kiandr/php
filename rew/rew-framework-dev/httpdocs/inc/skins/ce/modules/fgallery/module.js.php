/* <script> */
(function () {

    // Configure PhotoSwipe
    var photoSwipeOptions = {
        history: false,
        index: 0
    }, galleryCookie = 'open-gallery';

    // Handle 'View Gallery' click event
    var $gallery = $('#<?=$this->getUID(); ?>');
    var galleryPhotos = $gallery.data('photos');
    if (galleryPhotos && galleryPhotos.length > 0) {

        var $galleryLink = $('.action-gallery');
        var registerUrl = $galleryLink.data('register') || false;
        $galleryLink.on('click', function (e) {
            e.preventDefault();

            // Registration form
            if (registerUrl && registerUrl.length > 0) {
                $.Window({ iframe : registerUrl });
                BREW.Cookie(galleryCookie, 1);
                return false;
            }

            // Load & update gallery slide image
            var loadGalleryItem = function (item) {
                if (item.done) return;
                var img = new Image();
                $(img).on('load', function () {
                    item.w = img.naturalWidth;
                    item.h = img.naturalHeight;
                    gallery.invalidateCurrItems();
                    gallery.updateSize(true);
                    item.done = true;
                });
                $(img).on('error', function () {
                    item.done = true;
                });
                img.src = item.src;
            };

            // Load PhotoSwipe gallery
            var gallery = new PhotoSwipe(
                $gallery.find('.pswp').get(0),
                PhotoSwipeUI_Default,
                galleryPhotos,
                photoSwipeOptions
            );

            // Handle getting slide image
            gallery.listen('gettingData', function (index, item) {
                if (item.done) return;
                setTimeout(function () {
                    loadGalleryItem(item);
                }, 100)
            });

            // Handle loaded slide image
            gallery.listen('imageLoadComplete', function (index, item) {
                if (item.done) return;
                loadGalleryItem(item);
            });

            gallery.init();

        });

        // Open gallery on load
        if (!registerUrl && BREW.Cookie(galleryCookie) > 0) {
            $galleryLink.trigger('click');
            BREW.Cookie(galleryCookie, 0);
        }

    }

})();
/* </script> */