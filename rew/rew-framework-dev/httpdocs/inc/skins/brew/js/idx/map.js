(function () {

    // Require mapOptions
    if (!mapOptions) return;
    if (!mapOptions.containers) mapOptions.containers = {};
    mapOptions.containers = $.extend(true, {
        canvas: '#map_canvas',
        directions: '#directions',
        directionsForm: '#map-directions'
    }, mapOptions.containers);

    // Load Map
    var $map = $(mapOptions.containers.canvas).REWMap($.extend(true, mapOptions, {
        onInit : function () {

            // Setup Directions
            var $directions = $(mapOptions.containers.directions), directions = new REWMap.Directions({
                // Renderer Options
                renderer : {
                    map: this.getMap(), // Render on Map
                    panel: $directions.get(0) // Render to DOM Element
                },
                // On Success
                onSuccess: function () {
                    // Remove Any Errors
                    $directions.find('.msg.negative').remove();
                    // Show Print Button
                    $print.removeClass('hidden');
                },
                // On Failure
                onFailure: function (error) {
                    // Display Error
                    $directions.html('<div class="msg negative"><p>' + error + '</p></div>');
                    // Hide Print Button
                    $print.addClass('hidden');
                }
            });

            // Form Submit
            var $form = $(mapOptions.containers.directionsForm).on('submit', 'form', function () {
                var from = $form.find('input[name="from"]').val(), to = $form.find('input[name="to"]').val();
                directions.getDirections(from, to);
                return false;
            });

            // Print Button
            var $print = $('<button type="button" class="hidden">Print Directions</button>').on('click', function () {
                var w = window.open('about:blank');
                w.document.write($directions.html());
                w.document.close();
                w.focus();
                w.print();
            }).appendTo($form.find('.btnset'));

        }
    }));

})();
