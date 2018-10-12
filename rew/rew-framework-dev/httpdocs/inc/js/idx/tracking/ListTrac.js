tracking_ListTrac = (function() {
    // Initialize variables
    var _listing = [];
    var _account = null;
    var _page = null;
    var _register = false;
    var _loaded = false;

    /**
	 * App Name
	 */
    var Name = 'Listtrac';
	
    /**
	 * Get Listing From Meta Tag (Useful When Listing Page Is Loaded)
	 */
    var GetListingMeta = function () {
        listing_info = $('meta[name="tracking_ListTrac"]').attr('content');

        if (typeof(listing_info) == 'string') {
            _listing = JSON.parse(listing_info);
        } else {
            return null;
        }
	
        return _listing;
    };

    var Track = function(event_type, data) {
        if (typeof(data.AgentID) == 'undefined') {
            data.AgentID = null;
        }
        _LT._trackEvent(event_type, data.ListingMLS, data.AddressZipCode, data.AgentID);
    };

    var Ready = function() {
        // Internal callback that is called after the ListTrac JavaScript is
        // finished loading
        if (_loaded) {
            // Already loaded
            return;
        }
        _loaded = true;

        if (typeof(_LT) != 'undefined' && _LT != null) {
            // Initialize ListTrac if everything was successful
            _LT.initListTrac(_account);

            if (_register != false) {
                // We have a registration event to track
                Register(_register);
            }

            _listing = GetListingMeta();

            if (Tracking.listing_pages.indexOf(_page) !== -1) {
                // Some skins use modal HTML containers instead of iframes or popups. On those,
                // we'll still be on the details page.
                if (_page == 'details') {
                    // Track a view event.
                    Track(_eventType.view, _listing);
                }

                // We've shared with a friend.
                $(document).on('click', '#social-network-panel a', function () {
                    Track(_eventType.share, _listing);
                });

                $('.js-view-vtour, a.btn, a.action, a[title="Virtual Tour"]').filter(function() {
                    var $this = $(this);
                    return $this.hasClass('js-view-vtour') || $this.text() === 'Virtual Tour' || $this.text() === 'Tour';
                }).click(function() {
                    // Clicked on virtual tour link
                    Track(_eventType.vTour, _listing);
                });

                $('a.js-view-all-photos, a.btn.all').filter(function() {
                    var $this = $(this);
                    return $this.hasClass('js-view-all-photos') || $this.text() === 'All Photos';
                }).click(function() {
                    // Clicked on all photos link
                    Track(_eventType.gallery, _listing);
                });

                $('.js-save-listing, a#action-favorite, a#action-save, a.action-favorite, a.btn.save').click(function() {
                    var $this = $(this);
                    // Added to favorites on details page.
                    var $saved = $('#listing-details');

                    if ($saved.length > 0 && !$saved.hasClass('saved')) {
                        $saved = false;
                    }

                    if ($this.hasClass('js-save-listing') || (!$(this).find('.uk-icon-heart').length && $(this).text().indexOf('Remove') == -1 && $saved)) {
                        Track(_eventType.favorite, _listing);
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
        Register: function(agent_id) {
            // Track a registration event

            if (!_loaded) {
                // Not yet loaded... We'll have to try again when it finishes
                _register = agent_id;
                return;
            }

            if (_listing.length == 0) {
                // Can't register if there's no listing data available!
                return;
            }

            // Track the registration event
            data = GetListingMeta();
            data.AgentID = agent_id;
            Track(_eventType.lead, data);
        },

        /**
		 * Initialize list tracking
		 */
        Init: function(account, page) {
            // Initialize ListTrac
            _account = account;
            _page = page;

            var Loader = function () {
                // Create a script DOM element
                var head = document.getElementsByTagName('head')[0];
                var script = document.createElement('script');
                script.type = 'text/javascript';
                script.src = '//code.listtrac.com/monitor.ashx?acct=' + _account;
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
