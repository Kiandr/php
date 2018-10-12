$(function(){
    var $container = $('#idx-map-onboard');

    if ($container.length) {

    // Load Map
        var $map = $('#idx-map-onboard').REWMap($.extend(REW.mapOptions, {
            onInit: function () {

            // Shopping Icon
                iconShopping = new google.maps.MarkerImage('/img/map/marker-shopping@2x.png', null, null, null, new google.maps.Size(20, 25));

                // School Icon
                iconSchool = new google.maps.MarkerImage('/img/map/marker-school@2x.png', null, null, null, new google.maps.Size(20, 25));

                // Activate Tab
                var $active = $tabs.find('li.uk-active a');
                $active = ($active.length > 0) ? $active : $tabs.find('a:first');
                $active.trigger('click');
            }
        }));

        // Toggle Tabs
        var $tabs = $('div.tabbed-content .tabset').on('click', ' a', function () {
            var $this = $(this), $item = $this.parent('li');
            if (!$item.hasClass('uk-active')) {
            // Toggle Panel
                var panel = $this.data('panel'), $panel = $(panel);
                if ($panel.length > 0) {
                    $item.addClass('uk-active').siblings('li').removeClass('uk-active');
                    $panel.removeClass('hidden');
                    $panel.siblings('.panel').addClass('hidden');
                }
                // Toggle Schools
                var i = 0, l = schools.length, show = (panel == '#nearby-schools');
                for (i; i < l; i++) {
                    if (show) {
                        schools[i].show();
                    } else {
                        schools[i].hide();
                    }
                }
                // Toggle Amenities
                var i = 0, l = amenities.length, show = (panel == '#nearby-amenities');
                for (i; i < l; i++) {
                    if (show) {
                        amenities[i].show();
                    } else {
                        amenities[i].hide();
                    }
                }
                // Toggle Map
                var toggle = (panel == '#community-information') ? 'hide' : 'show';
                $map.REWMap(toggle, function () {
                // Already Loaded
                    if ($panel.hasClass('loaded')) return false;
                    $panel.html('<div class="uk-alert uk-alert-info"><p>Loading Results...</p></div>');
                    // Load Panel...
                    $.ajax({
                        'url': '?view=' + panel.replace('#', ''),
                        'type': 'POST',
                        'data': 'ajax=true',
                        'dataType': 'html',
                        'success': function (data) {
                            $panel.addClass('loaded').html(data);
                        }
                    });
                });
            }
            return false;
        });
    }
});
