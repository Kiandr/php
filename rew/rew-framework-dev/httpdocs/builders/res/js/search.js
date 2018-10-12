require(['brew'], function() {
    $(function() {

        // Add page specific class to body tag
        $('body').addClass('builder-search');

        // Autocomplete Fields
        $('#search-form').find('input.bdx-autocomplete').each(function () {
            var $input = $(this), multiple = $input.hasClass('single') ? false : true;
            $input.Autocomplete({
                multiple : multiple,
                url: '/builders/php/ajax/json.php',
                params : {
                    state: $('input[name="state"]').val()
                }
            });
        });

        // toggle the search open/closed for small screens
        $('.bdx-search-toggle .btn, .bdx-sidebar_search_form .close-search .btn').click(function(e) {
            e.preventDefault();
            $('.bdx-sidebar_search_form').toggleClass('visible');
        });

    });
});