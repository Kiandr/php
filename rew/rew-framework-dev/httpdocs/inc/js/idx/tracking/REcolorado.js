tracking_REcolorado = (function() {

    // Initialize variables
    var _listing = [];
    var _account = null;
    var _page = null;
    var _register = false;
    var _loaded = false;

    /**
     * App Name
     */
    var Name = 'Recolorado';

    /**
     * Get Listing From Meta Tag (Useful When Listing Page Is Loaded)
     */
    var GetListingMeta = function () {
        listing_info = $('meta[name="tracking_REcolorado"]').attr('content');

        if (typeof(listing_info) == 'string') {
            _listing = JSON.parse(listing_info);
        } else {
            return null;
        }

        return _listing;
    };

    var Track = function(event_type, data) {
        _tracker = new FireflyListingMetrics({
            apiKey: _account,
            listingNumber: data.ListingMLS,
            listingZipCode: data.AddressZipCode
        });

        switch (event_type) {
        case 'view':
            _tracker.TrackView();
            break;
        case 'favorite':
            _tracker.TrackFavorite();
            break;
        case 'inquiry':
            _tracker.TrackInquiry();
            break;
        case 'share':
            _tracker.TrackShare();
            break;
        case 'print':
            _tracker.TrackPrint();
            break;
        case 'directions':
            _tracker.TrackDirections();
            break;
        }
    };


    var Ready = function () {
        // Internal callback that is called after the ListTrac JavaScript is
        // finished loading
        if (_loaded) {
            // Already loaded
            return;
        }
        _loaded = true;

        if (typeof(FireflyListingMetrics) != 'undefined' && FireflyListingMetrics != null) {

            if (_register != false) {
                // We have a registration event to track
                Register(_register);
            }

            // If Listing Page
            if (Tracking.listing_pages.indexOf(_page) !== -1) {

                _listing = GetListingMeta();

                // Some skins use modal HTML containers instead of iframes or popups. On those,
                // we'll still be on the details page.
                if (_page == 'details') {
                    // Track a view event.
                    Track('view', _listing);
                } else if (_page == 'map') {
                    $('#map-directions button[type="submit"]').on('click', function () {
                        Track('directions', _listing);
                    });
                }

                // We've shared with a friend.
                $('#social-network-panel').find('a').on('click', function () {
                    Track('share', _listing);
                });

                // Inquired
                $('#inquire-allure button[type="submit"]').on('click', function () {
                    Track('inquiry', _listing);
                });

                $('#map-directions').on('click', function () {
                    Track('directions', _listing);
                });

                // Track Print Event (May Not Be Accurate)
                $('.print-details a').on('click', function () {
                    Track('print', _listing);
                });

                $('.js-save-listing, a#action-favorite, a#action-save, a.action-favorite, a.btn.save').on('click', function() {
                    var $this = $(this);
                    // Added to favorites on details page.
                    var $saved = $('#listing-details');

                    if ($saved.length > 0 && !$saved.hasClass('saved')) {
                        $saved = false;
                    }

                    if ($this.hasClass('js-save-listing') || (!$(this).find('.uk-icon-heart').length && $(this).text().indexOf('Remove') == -1 && $saved)) {
                        Track('favorite', _listing);
                    }

                    // Make sure if we add, remove, etc the listing we don't send extra events
                    $this.removeClass('js-save-listing');

                });
            } else {
                $('a[data-event="favorite"]').on('click', function() {
                    var $this = $(this);
                    // Added to favorites on details page.
                    var $saved = $('#listing-details');

                    if ($saved.length > 0 && !$saved.hasClass('saved')) {
                        $saved = false;
                    }

                    if ($this.hasClass('js-save-listing') || (!$(this).find('.uk-icon-heart').length && $(this).text().indexOf('Remove') == -1 && $saved)) {
                        _listing = $this.data('listing' + Name);
                        Track('favorite', _listing);
                    }

                    // Make sure if we add, remove, etc the listing we don't send extra events
                    $this.removeClass('js-save-listing');
                });
            }
        }
    };

    return {
        /**
         * Track registration event
         */
        Register: function (agent_id) {
            // RE Colorado does not track registration events at this time
            return;
        },

        /**
         * Initialize list tracking
         */
        Init: function (account, page) {
            // Initialize ListTrac
            _account = account;
            _page = page;

            var Loader = function () {
                // Create a script DOM element
                var head = document.getElementsByTagName('head')[0];
                var script = document.createElement('script');
                script.type = 'text/javascript';
                script.src = '//lmcdn.recolorado.com/js/firefly-listing-metrics.min.js';
                script.async = true;

                // Bind multiple events for cross-browser compatibility
                script.onreadystatechange = Ready;
                script.onload = Ready;

                // Append the script to the DOM
                head.appendChild(script);
            };

            if (window.$) {
                // jQuery is loaded already (old skin).
                Loader();
            } else {
                window.postScriptLoadingQueue = window.postScriptLoadingQueue || [];
                window.postScriptLoadingQueue.push(Loader);
            }
        }
    };
})();