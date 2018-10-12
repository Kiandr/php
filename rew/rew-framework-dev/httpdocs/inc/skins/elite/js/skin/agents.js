(function () {
    function adjustAgents () {
        // Make sure breakpoints have already been calculated. Currently they will be, but we want this to work whether
        // this file is included before breakpoint calculation or not, so it's less fragile.

        if (REW.breakpoints) {
            var index = 0;
            $('.fw-featured-agents .fa-inner-container').each(function () {
                var $this = $(this);
                var $container = $this.children('.fw-container');
                index++;
                $container.sort(function (a, b) {
                    // In super duper small res, sort dom so every other agent has its photo on the left
                    var cmp = $(a).hasClass('fw-container-photo');
                    if ((REW.breakpoints.maxName == 'breakpoint-xsmall' || REW.breakpoints.maxName == 'breakpoint-small') && index % 2 === 0) {
                        cmp = cmp ? 1 : -1;
                    } else {
                        cmp = cmp ? -1 : 1;
                    }
                    return cmp;
                });
                $container.detach().appendTo($this);
            });
        }
    }
    $(REW).on('bp-move', adjustAgents);
    adjustAgents();
})();
